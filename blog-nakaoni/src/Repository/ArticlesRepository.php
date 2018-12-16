<?php

namespace App\Repository;

use App\Entity\Articles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Articles|null find($id, $lockMode = null, $lockVersion = null)
 * @method Articles|null findOneBy(array $criteria, array $orderBy = null)
 * @method Articles[]    findAll()
 * @method Articles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticlesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Articles::class);
    }

    /**
     * Récupère la requête de tous les éléments
     * @return Query
     */
    public function findAllByOrderQuery(): Query
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.updatedAt', 'DESC')
            ->getQuery();
    }

    /**
     * Récupère tous les articles d'une catégorie donnée
     * @param $categorie
     * @return Query
     */
    public function findByCategorieQuery($categorie): Query
    {
        //retourne la requête SQL pour le paginator
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
     * @return \Doctrine\ORM\Query
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
     * Récupère les 3 premiers articles avec le plus grand nombre de vues
     * @return mixed
     */
    public function findByNbViews()
    {
        $query = $this->createQueryBuilder('a')
            ->orderBy('a.nbViews', 'DESC')
            ->setMaxResults('3')
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
     * @param $id
     * @return Query
     */
    public function findAllArticlesByUserQuery($id): Query
    {
        return $query = $this->createQueryBuilder('a')
            ->join('a.auteur', 'u')
            ->addSelect('u')
            ->where('u.id = :id')
            ->setParameter('id', $id)
            ->andWhere('a.public = true')
            ->orderBy('a.id', 'DESC')
            ->getQuery();
    }

    /**
     * Retourne les 3 articles les plus vus d'un auteur
     * @param $id
     * @return mixed
     */
    public function find3BestArticlesByUser($id)
    {
        $query = $this->createQueryBuilder('a')
            ->join('a.auteur', 'u')
            ->addSelect('u')
            ->where('u.id = :id')
            ->setParameter('id', $id)
            ->andWhere('a.public = true')
            ->orderBy('a.nbViews', 'DESC')
            ->setMaxResults(3)
            ->getQuery();

        return $query->getResult();
    }
}
