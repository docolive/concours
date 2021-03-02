<?php

namespace App\Repository;

use App\Entity\Procede;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Procede|null find($id, $lockMode = null, $lockVersion = null)
 * @method Procede|null findOneBy(array $criteria, array $orderBy = null)
 * @method Procede[]    findAll()
 * @method Procede[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProcedeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Procede::class);
    }

    /**
     * @return Procede[] Returns an array of Procede objects
     */
    
    public function findProcedes($categorie)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.categorie = :categorie')
            ->setParameter('categorie', $categorie)
            ->getQuery()
            ->getResult()
        ;
    }
    

    /*
    public function findOneBySomeField($value): ?Procede
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
