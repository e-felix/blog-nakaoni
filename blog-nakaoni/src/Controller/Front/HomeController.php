<?php

namespace App\Controller\Front;


use App\Entity\Article;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    CONST ARTICLES_PER_PAGE = 3;

    /**
     * HomePage
     * @Route("/")
     */
    public function index(): Response
    {
        $repository = $this->getDoctrine()->getRepository(Article::class);
        $rndArticles = $repository->findBy(
            array("public" => true),
            array("nbViews" => "DESC"),
            self::ARTICLES_PER_PAGE
        );

        $films = $repository->findXByCategorie(Article::FILMS_CATEGORY, self::ARTICLES_PER_PAGE);
        $series = $repository->findXByCategorie(Article::SERIES_CATEGORY, self::ARTICLES_PER_PAGE);
        $mangas = $repository->findXByCategorie(Article::MANGAS_CATEGORY, self::ARTICLES_PER_PAGE);
        $jeux = $repository->findXByCategorie(Article::GAMES_CATEGORY, self::ARTICLES_PER_PAGE);

        return $this->render(
            "index.html.twig",
            array(
                "films" => $films,
                "series" => $series,
                "mangas" => $mangas,
                "jeux" => $jeux,
                "rndArticles" => $rndArticles
            )
        );
    }
}