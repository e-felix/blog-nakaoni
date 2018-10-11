<?php

namespace App\Controller;

use App\Entity\Commentaires;
use App\Form\CommentairesType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CommentairesController extends Controller
{
    /*--- ADMINISTRATION ---*/

    /**
     * @Route("/admin/commentaires", name="app_admin_listeComm")
     * @param Request $request
     * @return Response
     */
    public function listeCommentaires(Request $request): Response
    {
        $repository = $this->getDoctrine()->getRepository(Commentaires::class);

        $comm = $repository->findAllByOrderQuery();

        //Récupération de l'instance Paginator
        $paginator  = $this->get('knp_paginator');

        //création de la pagination
        $pagination = $paginator->paginate(
            $comm,
            $request->query->getInt('page', 1),
            50
        );

        return $this->render("admin/commentaires/liste_commentaires.html.twig", [
            "commentaires" => $pagination
        ]);
    }

    /**
     * @Route("/admin/commentaires/update/{id}", name="app_admin_updateComm", requirements={"id":"\d+"})
     * @param Commentaires $comm
     * @param Request $request
     * @return Response
     */
    public function updateComm (Commentaires $comm, Request $request): Response
    {
        $isPublished = $comm->getStatut();

        $form = $this->createForm(CommentairesType::class, $comm);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $comm = $form->getData();

            if($request->request->get('isPublished'))
            {
                $comm->setStatut(true);
            } else {
                $comm->setStatut(false);
            }

            // Récupération de doctrine
            $manager = $this->getDoctrine()->getManager();

            // Enregistrement en BDD
            $manager->persist($comm);
            $manager->flush();

            $this->addFlash('success', 'Modifications enregistrées');

            return $this->redirectToRoute('app_admin_updateComm', [
                'id' => $comm->getId()
            ]);
        }


        return $this->render('admin/commentaires/update_commentaires.html.twig', [
            'form' => $form->createView(),
            'isPublished' => $isPublished,
            'comm' => $comm
        ]);
    }

    /**
     * @Route("/admin/commentaires/status/{id}", name="app_admin_statusComm", requirements={"id":"\d+"})
     * @param Commentaires $comm
     * @return Response
     */
    public function updateCommStatus(Commentaires $comm): Response
    {
        if($comm->getStatut() == 1){
            $comm->setStatut(false);
        } else {
            $comm->setStatut(true);
        }

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($comm);
        $manager->flush();

        return $this->redirectToRoute("app_admin_listeComm");
    }

    /**
     * @Route("/admin/commentaires/remove/{id}", name="app_admin_removeComm", requirements={"id":"\d+"})
     * @param Commentaires $comm
     * @return Response
     */
    public function removeUser(Commentaires $comm): Response
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($comm);
        $manager->flush();

        $this->addFlash('success', 'Le commentaire a bien été supprimé');

        return $this->redirectToRoute('app_admin_listeComm');
    }
}