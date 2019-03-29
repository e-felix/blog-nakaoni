<?php

namespace App\Controller;


use App\Entity\Article;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class HomeController
 * @package App\Controller
 */
class HomeController extends AbstractController
{
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
            3
        );

        $films = $repository->findXByCategorie("films", 3);
        $series = $repository->findXByCategorie("series", 3);
        $mangas = $repository->findXByCategorie("mangas", 3);
        $jeux = $repository->findXByCategorie("games", 3);

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

    /**
     * @Route("/admin", name="app_admin")
     *
     * @return Response
     */
    public function indexAdmin(): Response
    {
        return $this->render("admin/index.html.twig");
    }
}