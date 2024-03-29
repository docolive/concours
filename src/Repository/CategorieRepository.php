<?php

namespace App\Repository;

use App\Entity\Categorie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Categorie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Categorie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Categorie[]    findAll()
 * @method Categorie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategorieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Categorie::class);
    }

    public function findFromConcoursAndCategorie($concours,$categorieName){
        //dd($concours);
        return $this->createQueryBuilder('c')
        ->Join('c.type','t')
        ->where('t.concours = :concours')
        ->andWhere('c.name =:categoriename')
        ->setParameter('concours',$concours)
        ->setParameter('categoriename',$categorieName)
        ->getQuery()
        ->getResult()
        ;
    
    }

    public function findFromConcours($concours){
        return $this->createQueryBuilder('c')
        ->Join('c.type','t')
        ->where('t.concours = :concours')
        ->orderBy('c.name','ASC')
        ->setParameter('concours',$concours)
        ->getQuery()
        ->getResult()
        ;
    
    }

    

    public function findFromConcoursForTables($concours){
        return $this->createQueryBuilder('c')
        ->Join('c.type','t')
        ->where('t.concours = :concours')
        ->andWhere('c.participe = true')
        ->orderBy('c.name','ASC')
        ->setParameter('concours',$concours)
        ->getQuery()
        ->getResult()
        ;
    
    }

    

    // /**
    //  * @return Categorie[] Returns an array of Categorie objects
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
    public function findOneBySomeField($value): ?Categorie
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
