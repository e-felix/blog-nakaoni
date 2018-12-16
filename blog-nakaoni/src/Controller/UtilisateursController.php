<?php

namespace App\Controller;


use App\Entity\Articles;
use App\Entity\Utilisateurs;
use App\Form\UtilisateursType;
use Doctrine\ORM\Mapping\OrderBy;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UtilisateursController extends Controller
{

    /*--- FRONT ---*/

    /**
     * Affiche les articles d'un auteur
     * @Route("/utilisateur/{id}-{title}", name="app_user", requirements={"id":"\d+"})
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function index(int $id, Request $request): Response
    {
        //##todo: Récupère le profil d'un utilisateurs

        //##todo: Récupère les commentaires d'un utilisateurs

        //##todo: Limiter la recherche aux auteurs (Affiche les articles seulement si c'est un auteur)
        $repositoryArticles = $this->getDoctrine()->getRepository(Articles::class);

        //Récupère les articles d'un auteur

        $userArticles = $repositoryArticles->findAllArticlesByUserQuery($id);

        //R2cupère les 3 articles les plus vue d'un auteur
        $userBestArticles = $repositoryArticles->find3BestArticlesByUser($id);

        //Récupération de l'instance Paginator
        $paginator  = $this->get('knp_paginator');

        //création de la pagination
        $paginationArticles = $paginator->paginate(
            $userArticles,
            $request->query->getInt('page', 1),
            6
        );

        return $this->render('utilisateurs/index.html.twig', [
            'paginationArticles' => $paginationArticles,
            'userArticlesBest' => $userBestArticles
        ]);

    }


    /*--- ADMINISTRATION ---*/

    /**
     * @Route("/admin/utilisateurs", name="app_admin_listeUsers")
     * @param Request $request
     * @return Response
     */
    public function listeUtilisateurs(Request $request): Response
    {
        $repository = $this->getDoctrine()->getRepository(Utilisateurs::class);

        $users = $repository->findAllQuery();

        //Récupération de l'instance Paginator
        $paginator  = $this->get('knp_paginator');

        //création de la pagination
        $pagination = $paginator->paginate(
            $users,
            $request->query->getInt('page', 1),
            50
        );

        return $this->render("admin/users/liste_users.html.twig", [
            'users' => $pagination
        ]);

    }

    /**
     * @Route("/admin/utilisateurs/add/", name="app_admin_addUser")
     * @param Request $request
     * @return Response
     */
    public function addUser(Request $request): Response
    {
        //Récupération du manager de FOSUser
        $userManager = $this->get('fos_user.user_manager');

        //Création d'une instance
        $user = $userManager->createUser();

        $form = $this->createForm(UtilisateursType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $user = $form->getData();
            $user->setPlainPassword('Nak4on1');

            $roles = $request->request->get('roles');

            if(!empty($roles) && ($roles == 'ROLE_ABONNE' || $roles == 'ROLE_MODERATEUR' || $roles == 'ROLE_AUTEUR' || $roles == 'ROLE_ADMIN'))
            {
                $user->setRoles([$roles]);
            } else {
                $user->setRoles(['ROLE_ABONNE']);
                $this->addFlash('success', 'Erreur avec l\'attribution du rôle, ROLE_ABONNE a été attribué par défaut');
            }

            // Récupération de doctrine
            $manager = $this->getDoctrine()->getManager();

            // Enregistrement en BDD
            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', 'Utilisateur créé');

            return $this->redirectToRoute('app_admin_updateUser', [
                'id' => $user->getId()
            ]);
        }


        return $this->render('admin/users/add_user.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/utilisateurs/update/{id}", name="app_admin_updateUser", requirements={"id":"\d+"})
     * @param Utilisateurs $user
     * @param Request $request
     * @return Response
     */
    public function updateUser(Utilisateurs $user, Request $request): Response
    {
        $form = $this->createForm(UtilisateursType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $user = $form->getData();

            $roles = $request->request->get('roles');

            if(!empty($roles) && ($roles == 'ROLE_ABONNE' || $roles == 'ROLE_MODERATEUR' || $roles == 'ROLE_AUTEUR' || $roles == 'ROLE_ADMIN'))
            {
                $user->setRoles([$roles]);
            }

            // Récupération de doctrine
            $manager = $this->getDoctrine()->getManager();

            // Enregistrement en BDD
            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', 'Modifications enregistrées');
        }


        return $this->render('admin/users/update_user.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * @Route("/admin/utilisateur/status/{id}", name="app_admin_statusUser", requirements={"id":"\d+"})
     * @param Utilisateurs $user
     * @return Response
     */
    public function updateUserStatus(Utilisateurs $user): Response
    {
        if($user->isEnabled()){
            $user->setEnabled(false);
        } else {
            $user->setEnabled(true);
        }

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($user);
        $manager->flush();

        return $this->redirectToRoute("app_admin_listeUsers");
    }

    /**
     * @Route("/admin/utilisateur/remove/{id}", name="app_admin_removeUser", requirements={"id":"\d+"})
     * @param Utilisateurs $user
     * @return Response
     */
    public function removeUser(Utilisateurs $user): Response
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($user);
        $manager->flush();

        $this->addFlash('success', 'L\'utilisateur a bien été supprimé');

        return $this->redirectToRoute('app_admin_listeUsers');
    }
}