<?php

namespace App\Repository;

use App\Entity\Table;
use App\Entity\Concours;
use App\Entity\Categorie;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Table|null find($id, $lockMode = null, $lockVersion = null)
 * @method Table|null findOneBy(array $criteria, array $orderBy = null)
 * @method Table[]    findAll()
 * @method Table[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TableRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Table::class);
    }

    public function findAllConcours($concours){
        return $this->createQueryBuilder('t')
        ->innerJoin(
            Categorie::class,    // Entity
            'c',               // Alias
            Join::WITH,        // Join type
            't.categorie = c.id' // Join columns
        )
        ->where('c.concours = :concours')
        ->setParameter('concours',$concours)
        ->orderBy('t.name','ASC')
        ->getQuery()
        ->getResult()
        ;
    }

    // /**
    //  * @return Table[] Returns an array of Table objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Table
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
