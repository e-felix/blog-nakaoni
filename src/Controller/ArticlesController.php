<?php

namespace App\Controller;


use App\Entity\Articles;
use App\Form\ArticlesType;
use App\Entity\Commentaires;
use App\Form\CommentairesType;
use App\Controller\Traits\Util;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ArticlesController extends Controller
{
    use Util;
    /*--- FRONT ---*/

    /**
     * Affiche la HomePage Rubrique
     * @Route("/rubrique/{categorie}", name="app_rubrique", requirements={"categorie":"films|series|mangas|games" })
     * @param string $categorie
     * @param Request $request
     * @return Response
     */
    public function index(string $categorie, Request $request): Response
    {
        $repository = $this->getDoctrine()->getRepository(Articles::class);

        //Récupération des articles liés à la catégorie
        $articlesByCategorie = $repository->findByCategorieQuery($categorie);

        //Récupération des 3 articles les plus vus (by nbViews)
        $bestOfArticlesByCategorie = $repository->findByNbViewsByCategorie($categorie);

        //Récupération de l'instance Paginator
        $paginator  = $this->get('knp_paginator');

        //création de la pagination
        $pagination = $paginator->paginate(
            $articlesByCategorie,
            $request->query->getInt('page', 1),
            6
        );

        if (!$pagination) {
            throw $this->createNotFoundException('Erreur! Les articles n\'ont pas été trouvés.');
        }

        return $this->render('articles/index.html.twig', [
            'pagination' => $pagination,
            'articlesBest' => $bestOfArticlesByCategorie,
            'categorie' => $categorie
        ]);

    }

    /**
     * Affiche un article
     * @Route("rubrique/{categorie}/article/{id}-{title}", name="app_article", requirements={"categorie":"films|series|mangas|games", "id":"\d+"})
     * @param int $id
     * @param string $categorie
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function show(int $id, string $categorie, Request $request): Response
    {
        $repository = $this->getDoctrine()->getRepository(Articles::class);

        //Récupération de l'article via l'id
        $article = $repository->findOneArticle($id);

        $nbViews = $article->getNbViews();
        $article->setNbViews($nbViews + 1);

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($article);
        $manager->flush();

        //Commentaires
        $newCommentaries = new Commentaires();
        $form = $this->createForm(CommentairesType::class, $newCommentaries);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $newCommentaries = $form->getData();
            $user = $this->getUser();
            $dateTime = new \DateTime();

            $newCommentaries->setUtilisateurs($user);
            $newCommentaries->setArticles($article);
            $newCommentaries->setCreatedAt($dateTime);
            $newCommentaries->setStatut(1);

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($newCommentaries);
            $manager->flush();

            $titre = strtolower(str_replace(" ", "-", $article->getTitre()));

            return $this->redirectToRoute("app_article", [
                'categorie'=> $categorie, 
                'id' => $id, 
                'title' => $titre
            ]);
        }

        //Récupération des commentaires
        $commentaires = $article->getCommentaires();

        //Récupération des 3 articles les plus vus (by nbViews)
        $bestOfArticlesByCategorie = $repository->findByNbViewsByCategorie($categorie);

        return $this->render('articles/show.html.twig', [
            'article' => $article,
            'commentaires' => $commentaires,
            'articlesBest' => $bestOfArticlesByCategorie,
            'form' => $form->createView()
        ]);

    }

    /*--- ADMINISTRATION ---*/

    /**
     * @Route("/admin/article", name="app_admin_listeArticles")
     * @param Request $request
     * @return Response
     */
    public function listeArticles(Request $request): Response
    {
        $user = $this->getUser();

        $repository = $this->getDoctrine()->getRepository(Articles::class);

        $articles = $repository->findAllByOrderQuery();

        //Récupération de l'instance Paginator
        $paginator  = $this->get('knp_paginator');

        //création de la pagination
        $pagination = $paginator->paginate(
            $articles,
            $request->query->getInt('page', 1),
            50
        );

        return $this->render("admin/article/liste_articles.html.twig", [
            "articles" => $pagination,
            'user' => $user
        ]);
    }

    /**
     * @Route("/admin/article/add", name="app_admin_addArticle")
     * @param Request $request
     * @return Response
     */
    public function addArticle(Request $request): Response
    {
        $article = new Articles();
        $user = $this->getUser();
        $dateTime = new \DateTime();

        $form = $this->createForm(ArticlesType::class, $article);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $article = $form->getData();

            if($request->request->get('isPublished'))
            {
                $article->setPublic(true);
            } else {
                $article->setPublic(false);
            }

            $lienYT = $request->request->get('articles')['youtube'];

            if($lienYT)
            {
               $youtubeVariable = $this->extractYoutubeVariable($lienYT);
               $article->setYoutube($youtubeVariable);
            }

            // Attribution des valeurs par défaut
            $article->setAuteur($user);
            $article->setNBViews(0);
            $article->setLiked(0);
            $article->setDisliked(0);
            $article->setCreatedAt($dateTime);
            $article->setUpdatedAt($dateTime);
            $article->setCommentEnabled(0);

            // Récupération de doctrine
            $manager = $this->getDoctrine()->getManager();

            // Enregistrement en BDD
            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute("app_admin_listeArticles");
        }

        return $this->render("admin/article/ajout_article.html.twig", [
            "createForm" => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/article/update/{id}", name="app_admin_updateArticle", requirements={"id":"\d+"})
     * @param Articles $article
     * @param Request $request
     * @return Response
     */
    public function updateArticles(Articles $article, Request $request): Response
    {
        $updateDate = new \DateTime();

        $form = $this->createForm(ArticlesType::class, $article);

        $isPublished = $article->getPublic();

        $youtubeLinkDB = $article->getYoutube();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $article = $form->getData();

            if($request->request->get('isPublished'))
            {
                $article->setPublic(true);
            } else {
                $article->setPublic(false);
            }

            $lienYT = $request->request->get('articles')['youtube'];

            if($lienYT != $youtubeLinkDB)
            {
                $youtubeVariable = $this->extractYoutubeVariable($lienYT);
                $article->setYoutube($youtubeVariable);
            }

            $article->setUpdatedAt($updateDate);

            // Récupération de doctrine
            $manager = $this->getDoctrine()->getManager();

            // Enregistrement en BDD
            $manager->persist($article);
            $manager->flush();

            $this->addFlash('success', 'Modifications enregistrées');

            return $this->redirectToRoute('app_admin_updateArticle', [
                'id' => $article->getId()
            ]);
        }


        return $this->render('admin/article/update_article.html.twig', [
            'form' => $form->createView(),
            'isPublished' => $isPublished,
            'article' => $article
        ]);
    }

    /**
     * @Route("/admin/article/status/{id}", name="app_admin_statusArticle", requirements={"id":"\d+"})
     * @param Articles $article
     * @return Response
     */
    public function updateArticleStatus(Articles $article): Response
    {
        $updateDate = new \DateTime();

        if($article->getPublic() == 1){
            $article->setPublic(false);
        } else {
            $article->setPublic(true);
        }

        $article->setUpdatedAt($updateDate);

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($article);
        $manager->flush();

        return $this->redirectToRoute("app_admin_listeArticles");
    }

    /**
     * @Route("/admin/article/remove/{id}", name="app_admin_removeArticle", requirements={"id":"\d+"})
     * @param Articles $article
     * @return Response
     */
    public function removeArticle(Articles $article): Response
    {
        $manager = $this->getDoctrine()->getManager();

        $manager->remove($article);
        $manager->flush();

        $this->addFlash('success', 'L\'article a bien été supprimé');

        return $this->redirectToRoute('app_admin_listeArticles');
    }
}