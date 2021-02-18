<?php

namespace App\Repository;

use App\Entity\Echantillon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Echantillon|null find($id, $lockMode = null, $lockVersion = null)
 * @method Echantillon|null findOneBy(array $criteria, array $orderBy = null)
 * @method Echantillon[]    findAll()
 * @method Echantillon[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EchantillonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Echantillon::class);
    }

    // /**
    //  * @return Echantillon[] Returns an array of Echantillon objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Echantillon
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
