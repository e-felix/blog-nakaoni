<?php

namespace App\Controller\Front;


use App\Entity\Article;
use App\Entity\Utilisateur;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Doctrine\ORM\EntityManagerInterface;

use FOS\UserBundle\Model\UserManagerInterface;

use Knp\Component\Pager\PaginatorInterface;

class UtilisateurController extends AbstractController
{
    CONST ARTICLES_PER_PAGE = 6;

    /**
     * @param EntityManagerInterface $entityManager
     * @param PaginatorInterface $paginator
     */
    public function __construct(
        PaginatorInterface $paginator,
        EntityManagerInterface $entityManager,
        UserManagerInterface $userManager
    ) {
        $this->entityManager = $entityManager;

        $this->userManager = $userManager;

        $this->repository = $this->entityManager->getRepository(Utilisateur::class);

        $this->paginator = $paginator;
    }

    /*--- FRONT ---*/

    /**
     * Affiche les articles d'un auteur
     * @Route(
     *     "/utilisateur/{id}-{username}/articles",
     *     name="app_user",
     *     requirements={"id":"\d+"}
     * )
     *
     * @param int $id
     * @param Request $request
     */
    public function index(
        Utilisateur $user,
        Request $request
    ): Response
    {
        #todo: Récupère le profil d'un utilisateur

        #todo: Récupère les commentaires d'un utilisateurs

        #todo: Limiter la recherche aux auteurs (Affiche les articles seulement si c'est un auteur)
        $repositoryArticles = $this->getDoctrine()->getRepository(Article::class);

        $userArticles = $repositoryArticles->findAllArticlesByUserQuery($user);
        $userBestArticles = $repositoryArticles->find3BestArticlesByUser($user);

        $paginationArticles = $this->paginator->paginate(
            $userArticles,
            $request->query->getInt("page", 1),
            self::ARTICLES_PER_PAGE
        );

        return $this->render(
            "utilisateurs/index.html.twig",
            array(
                "paginationArticles" => $paginationArticles,
                "userArticlesBest" => $userBestArticles
            )
        );
    }
}