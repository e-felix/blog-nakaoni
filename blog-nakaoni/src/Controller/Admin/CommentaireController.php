<?php

namespace App\Controller\Admin;

use App\Entity\Commentaire;
use App\Form\CommentaireType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Doctrine\ORM\EntityManagerInterface;

use Knp\Component\Pager\PaginatorInterface;

class CommentaireController extends AbstractController
{
    CONST COMMENTAIRES_PER_PAGE = 50;

    /**
     * @param EntityManagerInterface $entityManager
     * @param PaginatorInterface $paginator
     */
    public function __construct(
        PaginatorInterface $paginator,
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;

        $this->repository = $this->entityManager->getRepository(Commentaire::class);

        $this->paginator = $paginator;
    }

    /**
     * @Route(
     *     "/admin/commentaire",
     *     name="app_admin_listeComm"
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function listeCommentaires(Request $request): Response
    {
        $commentaires = $this->repository->findAllByOrderQuery();

        $pagination = $this->paginator->paginate(
            $commentaires,
            $request->query->getInt("page", 1),
            self::COMMENTAIRES_PER_PAGE
        );

        return $this->render(
            "admin/commentaires/liste_commentaires.html.twig",
            array("commentaires" => $pagination)
        );
    }

    /**
     * @Route("
     *     /admin/commentaires/update/{id}",
     *     name="app_admin_updateComm",
     *     requirements={"id":"\d+"}
     * )
     *
     * @param Commentaire $commentaire
     * @param Request $request
     */
    public function updateComm(
        Commentaire $commentaire,
        Request $request
    ): Response
    {
        $isPublished = $commentaire->getStatut();

        $form = $this->createForm(CommentairesType::class, $commentaire);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $commentaire = $form->getData();

            if($request->request->get("isPublished"))
            {
                $commentaire->setStatut(true);
            } else {
                $commentaire->setStatut(false);
            }

            $this->entityManager->persist($commentaire);
            $this->entityManager->flush();

            $this->addFlash("success", "Modifications enregistrées");

            return $this->redirectToRoute(
                "app_admin_updateComm",
                array("id" => $commentaire->getId())
            );
        }

        return $this->render(
            "admin/commentaires/update_commentaires.html.twig",
            array(
                "form" => $form->createView(),
                "isPublished" => $isPublished,
                "comm" => $commentaire
            )
        );
    }

    /**
     * @Route(
     *     "/admin/commentaires/status/{id}",
     *     name="app_admin_statusComm",
     *     requirements={"id":"\d+"}
     * )
     *
     * @param Commentaire $commentaire
     * @return Response
     */
    public function updateCommStatus(Commentaire $commentaire): Response
    {
        if($commentaire->getStatut() === 1){
            $commentaire->setStatut(false);
        } else {
            $commentaire->setStatut(true);
        }

        $this->entityManager->persist($commentaire);
        $this->entityManager->flush();

        return $this->redirectToRoute("app_admin_listeComm");
    }

    /**
     * @Route(
     *     "/admin/commentaires/remove/{id}",
     *     name="app_admin_removeComm",
     *     requirements={"id":"\d+"}
     * )
     * @param Commentaire $commentaire
     * @return Response
     */
    public function removeCommentaire(Commentaire $commentaire): Response
    {
        $this->entityManager->remove($commentaire);
        $this->entityManager->flush();

        $this->addFlash("success", "Le commentaire a bien été supprimé");

        return $this->redirectToRoute("app_admin_listeComm");
    }
}