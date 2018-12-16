<?php

namespace App\Repository;

use App\Entity\Commentaires;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Commentaires|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commentaires|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commentaires[]    findAll()
 * @method Commentaires[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentairesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Commentaires::class);
    }


    /**
     * Récupère la requête de tous les éléments
     * @return Query
     */
    public function findAllByOrderQuery(): Query
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.createdAt', 'DESC')
            ->orderBy('c.articles', 'DESC')
            ->getQuery();
    }

//    /**
//     * @return Commentaires[] Returns an array of Commentaires objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Commentaires
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
