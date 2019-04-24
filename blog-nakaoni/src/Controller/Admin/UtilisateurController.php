<?php

namespace App\Controller\Admin;


use App\Entity\Article;
use App\Entity\Utilisateur;
use App\Form\UtilisateurType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Doctrine\ORM\EntityManagerInterface;

use FOS\UserBundle\Model\UserManagerInterface;

use Knp\Component\Pager\PaginatorInterface;

class UtilisateurController extends AbstractController
{
    CONST USERS_PER_PAGE = 50;

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

    /*--- ADMINISTRATION ---*/

    /**
     * @Route(
     *     "/admin/utilisateurs",
     *     name="app_admin_listeUsers"
     * )
     *
     * @param Request $request
     */
    public function listeUtilisateurs(Request $request): Response
    {
        $users = $repository->findAllQuery();

        $pagination = $this->paginator->paginate(
            $users,
            $request->query->getInt("page", 1),
            self::USERS_PER_PAGE
        );

        return $this->render(
            "admin/users/liste_users.html.twig",
            array("users" => $pagination)
        );

    }

    /**
     * @Route(
     *     "/admin/utilisateurs/add/",
     *     name="app_admin_addUser"
     * )
     *
     * @param Request $request
     */
    public function addUser(Request $request): Response
    {
        $user = $this->userManager->createUser();

        $form = $this->createForm(UtilisateurType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $user = $form->getData();

            $password = md5(uniqid());
            $user->setPlainPassword($password);

            $roles = $request->request->get("roles");

            if(
                !empty($roles) &&
                (
                    $roles == "ROLE_ABONNE" ||
                    $roles == "ROLE_MODERATEUR" ||
                    $roles == "ROLE_AUTEUR" ||
                    $roles == "ROLE_ADMIN"
                )
            )
            {
                $user->setRoles(array($roles));
            } else {
                $user->setRoles(array("ROLE_ABONNE"));
                $this->addFlash("success", "Erreur avec l\'attribution du rôle, ROLE_ABONNE a été attribué par défaut");
            }

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash("success", "Utilisateur créé");

            return $this->redirectToRoute(
                "app_admin_updateUser",
                array(
                    "id" => $user->getId()
                )
            );
        }

        return $this->render(
            "admin/users/add_user.html.twig",
            array(
                "form" => $form->createView()
            )
        );
    }

    /**
     * @Route(
     *     "/admin/utilisateurs/update/{id}",
     *     name="app_admin_updateUser",
     *     requirements={"id":"\d+"}
     * )
     *
     * @param Utilisateur $user
     * @param Request $request
     */
    public function updateUser(
        Utilisateur $user,
        Request $request
    ): Response
    {
        $form = $this->createForm(UtilisateurType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $user = $form->getData();

            $roles = $request->request->get("roles");

            if(
                !empty($roles) &&
                (
                    $roles == "ROLE_ABONNE" ||
                    $roles == "ROLE_MODERATEUR" ||
                    $roles == "ROLE_AUTEUR" ||
                    $roles == "ROLE_ADMIN"
                )
            )
            {
                $user->setRoles([$roles]);
            }

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash("success", "Modifications enregistrées");
        }


        return $this->render(
            "admin/users/update_user.html.twig",
            array(
                "form" => $form->createView(),
                "user" => $user
            )
        );
    }

    /**
     * @Route(
     *     "/admin/utilisateur/status/{id}",
     *     name="app_admin_statusUser",
     *     requirements={"id":"\d+"}
     * )
     *
     * @param Utilisateur $user
     */
    public function updateUserStatus(Utilisateur $user): Response
    {
        if($user->isEnabled()){
            $user->setEnabled(false);
        } else {
            $user->setEnabled(true);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->redirectToRoute("app_admin_listeUsers");
    }

    /**
     * @Route(
     *     "/admin/utilisateur/remove/{id}",
     *     name="app_admin_removeUser",
     *     requirements={"id":"\d+"}
     * )
     * @param Utilisateur $user
     * @return Response
     */
    public function removeUser(Utilisateur $user): Response
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        $this->addFlash("success", "L\'utilisateur a bien été supprimé");

        return $this->redirectToRoute("app_admin_listeUsers");
    }
}