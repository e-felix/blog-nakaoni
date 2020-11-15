<?php

namespace App\Repository;

use App\Entity\Commentaire;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;

use Doctrine\Persistence\ManagerRegistry;

/**
 *
 */
class CommentaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commentaire::class);
    }

    public function findAllByOrderQuery(): Query
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.createdAt', 'DESC')
            ->orderBy('c.article', 'DESC')
            ->getQuery();
    }
}
