<?php
/**
 * Created by PhpStorm.
 * User: stagiaire
 * Date: 25/06/2018
 * Time: 14:56
 */

namespace App\Controller;


use App\Entity\Articles;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HomeController
 * @package App\Controller
 */
class HomeController extends Controller
{
    /**
     * HomePage
     * @Route("/")
     */
    public function index(): Response
    {
        //Récupérations de 3 articles par catégorie
        $repository = $this->getDoctrine()->getRepository(Articles::class);
        $films = $repository->findXByCategorie('films', 3);
        $series = $repository->findXByCategorie('series', 3);
        $mangas = $repository->findXByCategorie('mangas', 3);
        $jeux = $repository->findXByCategorie('games', 3);

        //Récupérations des 3 articles avec le plus de vues toutes catégories confondues
        $rndArticles = $repository->findByNbViews();


        return $this->render('index.html.twig', [
            'films' => $films,
            'series' => $series,
            'mangas' => $mangas,
            'jeux' => $jeux,
            'rndArticles' => $rndArticles
        ]);
    }
}