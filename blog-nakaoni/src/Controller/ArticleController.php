<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Entity\Commentaire;
use App\Form\CommentaireType;

use App\Controller\Traits\Util;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Doctrine\ORM\EntityManagerInterface;

use Knp\Component\Pager\PaginatorInterface;

class ArticleController extends AbstractController
{
    use Util;

    /**
     * @param EntityManagerInterface $entityManager
     * @param PaginatorInterface $paginator
     */
    public function __construct(
        PaginatorInterface $paginator,
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;

        $this->repository = $this->entityManager->getRepository(Article::class);

        $this->paginator = $paginator;
    }

    /**
     * Récupère les 3 articles les plus vues de la catégorie
     *
     * @param string $categorie
     */
    private function bestOfArticlesByCategorie($categorie) {

        return $this->repository->findBy(
            array(
                "categorie" => $categorie,
                "public" => true
            ),
            array("createdAt" => "DESC"),
            3
        );
    }

    /*--- FRONT ---*/

    /**
     * Affiche la HomePage Rubrique
     *
     * @Route(
     *     "/rubrique/{categorie}",
     *     name="app_rubrique",
     *     requirements={
     *         "categorie":"films|series|mangas|games"
     *     }
     * )
     *
     * @param string $categorie
     * @param Request $request
     */
    public function index(
        string $categorie,
        Request $request
    ): Response
    {
        $articlesByCategorie = $this->repository->findByCategorieQuery($categorie);
        $bestOfArticlesByCategorie = $this->bestOfArticlesByCategorie($categorie);

        $pagination = $this->paginator->paginate(
            $articlesByCategorie,
            $request->query->getInt("page", 1),
            6
        );

        return $this->render(
            "articles/index.html.twig",
            array(
                "pagination" => $pagination,
                "articlesBest" => $bestOfArticlesByCategorie,
                "categorie" => $categorie
            )
        );

    }

    /**
     * Affiche un article
     *
     * @Route(
     *     "rubrique/{categorie}/article/{id}-{title}",
     *     name="app_article",
     *     requirements={
     *         "categorie":"films|series|mangas|games",
     *         "id":"\d+"
     *     }
     * )
     *
     * @param Article $article
     * @param string $categorie
     * @param Request $request
     *
     */
    public function show(
        Article $article,
        string $categorie,
        Request $request
    ): Response
    {
        $nbViews = $article->getNbViews();
        $commentaires = $article->getCommentaires();
        $bestOfArticlesByCategorie = $this->bestOfArticlesByCategorie($categorie);

        $commentaire = new Commentaire();

        $form = $this->createForm(CommentaireType::class, $commentaire);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

            $commentaire->setUtilisateurs($user);
            $commentaire->setArticle($article);
            $commentaire->setCreatedAt(new \DateTime("now"));
            $commentaire->setStatut(1);

            $this->entityManager->persist($commentaire);
            $this->entityManager->flush();

            $titre = strtolower(str_replace(" ", "-", $article->getTitre()));

            return $this->redirectToRoute(
                "app_article",
                array(
                    "categorie"=> $categorie,
                    "id" => $article->getId(),
                    "title" => $titre
                )
            );
        }

        $article->setNbViews($nbViews + 1);

        $this->entityManager->persist($article);
        $this->entityManager->flush();

        return $this->render(
            "articles/show.html.twig",
            array(
                "article" => $article,
                "commentaires" => $commentaires,
                "articlesBest" => $bestOfArticlesByCategorie,
                "form" => $form->createView()
            )
        );

    }

    /*--- ADMINISTRATION ---*/

    /**
     * @Route(
     *     "/admin/article",
     *     name="app_admin_listeArticles"
     * )
     *
     * @param Request $request
     */
    public function listeArticles(Request $request): Response
    {
        $user = $this->getUser();

        $articles = $this->repository->findAllByOrderQuery();

        $pagination = $this->paginator->paginate(
            $articles,
            $request->query->getInt("page", 1),
            50
        );

        return $this->render(
            "admin/article/liste_articles.html.twig",
            array(
                "articles" => $pagination,
                "user" => $user
            )
        );
    }

    /**
     * @Route(
     *     "/admin/article/add",
     *     name="app_admin_addArticle"
     * )
     *
     * @param Request $request
     */
    public function addArticle(Request $request): Response
    {
        $article = new Article();
        $user = $this->getUser();

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $article = $form->getData();

            if($request->request->get("isPublished"))
            {
                $article->setPublic(true);
            } else {
                $article->setPublic(false);
            }

            $lienYT = $request->request->get("articles")["youtube"];

            if($lienYT)
            {
               $youtubeVariable = $this->extractYoutubeVariable($lienYT);
               $article->setYoutube($youtubeVariable);
            }

            $article->setAuteur($user);
            $article->setNBViews(0);
            $article->setLiked(0);
            $article->setDisliked(0);
            $article->setCreatedAt(new \DateTime("now"));
            $article->setUpdatedAt(new \DateTime("now"));
            $article->setCommentEnabled(0);

            $this->entityManager->persist($article);
            $this->entityManager->flush();

            return $this->redirectToRoute("app_admin_listeArticles");
        }

        return $this->render(
            "admin/article/ajout_article.html.twig",
            array(
                "createForm" => $form->createView()
            )
        );
    }

    /**
     * @Route(
     *     "/admin/article/update/{id}",
     *     name="app_admin_updateArticle",
     *     requirements={"id":"\d+"}
     * )
     *
     * @param Article $article
     * @param Request $request
     */
    public function updateArticle(
        Article $article,
        Request $request
    ): Response
    {
        $form = $this->createForm(ArticleType::class, $article);

        $isPublished = $article->getPublic();
        $youtubeLinkDB = $article->getYoutube();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $article = $form->getData();

            if($request->request->get("isPublished"))
            {
                $article->setPublic(true);
            } else {
                $article->setPublic(false);
            }

            $lienYT = $request->request->get("articles")["youtube"];

            if($lienYT != $youtubeLinkDB)
            {
                $youtubeVariable = $this->extractYoutubeVariable($lienYT);
                $article->setYoutube($youtubeVariable);
            }

            $article->setUpdatedAt(new \DateTime("now"));

            $this->entityManager->persist($article);
            $this->entityManager->flush();

            $this->addFlash("success", "Modifications enregistrées");

            return $this->redirectToRoute(
                "app_admin_updateArticle",
                array(
                    "id" => $article->getId()
                )
            );
        }

        return $this->render(
            "admin/article/update_article.html.twig",
            array(
                "form" => $form->createView(),
                "isPublished" => $isPublished,
                "article" => $article
            )
        );
    }

    /**
     * @Route(
     *     "/admin/article/status/{id}",
     *     name="app_admin_statusArticle",
     *     requirements={"id":"\d+"}
     * )
     *
     * @param Article $article
     */
    public function updateArticleStatus(Article $article): Response
    {
        $updateDate = new \DateTime();

        if($article->getPublic() === 1){
            $article->setPublic(false);
        } else {
            $article->setPublic(true);
        }

        $article->setUpdatedAt(new \DateTime("now"));

        $this->entityManager->persist($article);
        $this->entityManager->flush();

        return $this->redirectToRoute("app_admin_listeArticles");
    }

    /**
     * @Route(
     *     "/admin/article/remove/{id}",
     *     name="app_admin_removeArticle",
     *     requirements={"id":"\d+"}
     * )
     *
     * @param Article $article
     */
    public function removeArticle(Article $article): Response
    {
        $this->entityManager->remove($article);
        $this->entityManager->flush();

        $this->addFlash("success", "L\'article a bien été supprimé");

        return $this->redirectToRoute("app_admin_listeArticles");
    }
}