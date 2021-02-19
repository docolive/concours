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

    /**
    * @return Echantillon[] Returns an array of Echantillon objects
    */
    
    public function findAllEchUser($user,$concours)
    {
        return $this->createQueryBuilder('e')
            ->innerJoin('e.categorie','c')
            ->innerJoin('c.type','t','WITH','t.concours = :concours')
            ->where('e.user = :user')
            ->setParameter('concours', $concours)
            ->setParameter('user', $user)
            ->orderBy('e.categorie', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

     /**
    * @return Echantillon[] Returns an array of Echantillon objects
    */
    
    public function findEchMemeType($user,$type)
    {
        return $this->createQueryBuilder('e')
            ->innerJoin('e.categorie','c')
            ->innerJoin('c.type','t','WITH','c.type = :type')
            ->where('e.user = :user')
            ->setParameter('type', $type)
            ->setParameter('user', $user)
            ->orderBy('e.categorie', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
    

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
