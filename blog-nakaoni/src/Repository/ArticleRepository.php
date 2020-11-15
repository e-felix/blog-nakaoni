<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Utilisateur;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;

use Doctrine\Persistence\ManagerRegistry;

/**
 *
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * Récupère la requête de tous les éléments
     */
    public function findAllByOrderQuery(): Query
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.updatedAt', 'DESC')
            ->getQuery();
    }

    /**
     * Récupère tous les articles d'une catégorie donnée
     *
     * @param $categorie
     */
    public function findByCategorieQuery($categorie): Query
    {
        return $query = $this->createQueryBuilder('a')
            ->where('a.categorie = :cat')
            ->setParameter('cat', $categorie)
            ->andWhere('a.public = true')
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery();
    }

    /**
     * Récupère X articles d'une catégorie donnée
     * @param $categorie
     * @param $x
     */
    public function findXByCategorie($categorie, $x)
    {
        $query = $this->createQueryBuilder('a')
            ->where('a.categorie = :cat')
            ->setParameter('cat', $categorie)
            ->andWhere('a.public = true')
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults($x)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * Récupère les 3 premiers articles avec le plus grand nombre de vues pour une catégorie donnée
     * @param $categorie
     * @return mixed
     */
    public function findByNbViewsByCategorie($categorie)
    {
        $query = $this->createQueryBuilder('a')
            ->where('a.categorie = :cat')
            ->setParameter('cat', $categorie)
            ->orderBy('a.nbViews', 'DESC')
            ->andWhere('a.public = true')
            ->setMaxResults('3')
            ->getQuery();

        return $query->getResult();
    }

    /**
     * Récupère un article via son Id
     * @param int $id
     * @return Articles|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneArticle(int $id): ?Articles
    {
        $query = $this->createQueryBuilder('a')
            ->join('a.auteur', 'u')
            ->addSelect('u')
            ->where('a.id = :id')
            ->andWhere('a.public = true')
            ->setParameter('id', $id)
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    /**
     * Retourne requête de la liste des articles d'un auteur pour knp_paginator
     *
     * @param Utilisateur $user
     */
    public function findAllArticlesByUserQuery(Utilisateur $user): Query
    {
        return $query = $this->createQueryBuilder('a')
            ->where('a.auteur = :user')
            ->setParameter('user', $user)
            ->andWhere('a.public = true')
            ->orderBy('a.id', 'DESC')
            ->getQuery();
    }

    /**
     * Retourne les 3 articles les plus vus d'un auteur
     * @param Utilisateur $user
     * @return mixed
     */
    public function find3BestArticlesByUser(Utilisateur $user)
    {
        $query = $this->createQueryBuilder('a')
            ->where('a.auteur = :user')
            ->setParameter('user', $id)
            ->andWhere('a.public = true')
            ->orderBy('a.nbViews', 'DESC')
            ->setMaxResults(3)
            ->getQuery();

        return $query->getResult();
    }
}
