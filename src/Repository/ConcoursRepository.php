<?php

namespace App\Repository;

use App\Entity\Concours;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Concours|null find($id, $lockMode = null, $lockVersion = null)
 * @method Concours|null findOneBy(array $criteria, array $orderBy = null)
 * @method Concours[]    findAll()
 * @method Concours[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConcoursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Concours::class);
    }

    /**
      * @return Concours[] Returns an array of Concours objects
    */
    public function findConcoursOuverts($user){
        $roles = $user->getRoles();

        if(in_array("ROLE_ADMIN",$roles) || in_array("ROLE_SUPER-ADMIN",$roles)){
            return $this->createQueryBuilder('c')
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        }else{
            if(in_array("ROLE_CANDIDAT",$roles)){
            return $this->createQueryBuilder('c')
            ->where('c.debut_inscription <= :now')
            ->andWhere('c.fin_inscription >= :now')
            ->orderBy('c.id', 'ASC')
            ->setParameter('now',date('Y-m-d'))
            ->getQuery()
            ->getResult()
        ;
            }
        }
    }

    // /**
    //  * @return Concours[] Returns an array of Concours objects
    //  */
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
    public function findOneBySomeField($value): ?Concours
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
