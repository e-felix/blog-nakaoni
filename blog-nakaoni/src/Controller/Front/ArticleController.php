<?php

namespace App\Controller\Front;

use App\Entity\Article;
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
}