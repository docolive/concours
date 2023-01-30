<?php

namespace App\Repository;

use App\Entity\Type;
use App\Entity\User;
use App\Entity\Profil;
use App\Entity\Categorie;
use App\Entity\Echantillon;
use App\Entity\Medaille;
use App\Entity\Procede;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

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
    
    public function findPalmares($concours)
    {
        return $this->createQueryBuilder('e')
        ->innerJoin('e.categorie','c')
        ->innerJoin('c.type','t','WITH','t.concours = :concours')
        ->innerJoin('e.medaille','m')
        ->innerJoin('e.user','u')
        ->innerJoin('u.profil','p')
        ->setParameter('concours', $concours)
        ->orderBy('e.categorie', 'ASC')
        ->addOrderBy('e.procede','ASC')
        ->addOrderBy('e.medaille','ASC')
        ->addOrderBy('p.nom','ASC')

        ->getQuery()
        ->getResult()
    ;
    }

    /**
    * @return Echantillon[] Returns an array of Echantillon objects
    */
    
    public function findSearch($param,$concours)
    {
        return $this->createQueryBuilder('e')
            ->Join(
                Categorie::class,    // Entity
                'c',               // Alias
                Join::WITH,        // Join type
                'e.categorie = c.id' // Join columns
            )
            ->Join(
                Type::class,    // Entity
                't',               // Alias
                Join::WITH,        // Join type
                'c.type = t.id' // Join columns
            )
            ->Join(
                User::class,    // Entity
                'u',               // Alias
                Join::WITH,        // Join type
                'e.user = u.id' // Join columns
            )
            ->innerJoin('u.profil','p')
            ->where('t.concours = :concours')
            ->andWhere('c.name LIKE :param')
            ->orWhere('e.public_ref LIKE :param')
            ->orWhere('p.nom LIKE :param')
            ->orWhere('p.raison_sociale LIKE :param')
            ->setParameter('concours', $concours)
            ->setParameter('param', "%".$param."%")
            ->orderBy('e.categorie', 'ASC')
            ->getQuery()
            ->getResult()
        ;
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
    public function findEchMemeCategorie($user,$categorie)
    {
        return $this->createQueryBuilder('e')
            ->where('e.user = :user')
            ->andWhere('e.categorie = :categorie')
            ->setParameter('categorie', $categorie)
            ->setParameter('user', $user)
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

    /**
    * @return Echantillon[] Returns an array of Echantillon objects
    */
    public function findEchMemeTypeOT($user,$variety,$categorie)
    {
        return $this->createQueryBuilder('e')
            ->innerJoin('e.categorie','c')
            ->innerJoin('c.type','t','WITH','t.otable = true')
            ->where('e.user = :user')
            ->andWhere('e.categorie = :categorie')
            ->andWhere('e.variety = :variety')
            ->setParameter('categorie', $categorie)
            ->setParameter('variety', $variety)
            ->setParameter('user', $user)
            ->orderBy('e.categorie', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    

    /**
    * @return Echantillon[] Returns an array of Echantillon objects
    */
    
    public function findEchConcours($concours)
    {
        return $this->createQueryBuilder('e')
            ->innerJoin('e.categorie','c')
            ->innerJoin('c.type','t','WITH','t.concours = :concours')
            // ->innerJoin('e.medaille','m')
            ->innerJoin('e.user','u')
            ->innerJoin('u.profil','p')
            ->setParameter('concours', $concours)
            ->orderBy('e.categorie', 'ASC')
            ->addOrderBy('e.procede','ASC')
            // ->addOrderBy('e.medaille','ASC')
            ->addOrderBy('p.nom','ASC')

            ->getQuery()
            ->getResult()
        ;
    }

    /**
    * @return Echantillon[] Returns an array of Echantillon objects
    */
    
    public function findEchCategorie($categorie)
    {
        return $this->createQueryBuilder('e')
            ->innerJoin('e.categorie','c')
            ->where('e.categorie = :categorie')
            ->setParameter('categorie', $categorie)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
    * @return Echantillon[] Returns an array of Echantillon objects
    */
    
    public function findEchCategorieWithMedaille($categorie,$medaille)
    {
        return $this->createQueryBuilder('e')
            ->innerJoin('e.categorie','c')
            ->where('e.categorie = :categorie')
            ->andWhere('e.medaille = :medaille')
            ->setParameter('categorie', $categorie)
            ->setParameter('medaille', $medaille)
            ->getQuery()
            ->getResult()
        ;
    }

     /**
    * @return Echantillon[] Returns an array of Echantillon objects
    */
    
    public function findEchCategorieWithMedailleAndProcede($categorie,$medaille,$procede)
    {
        return $this->createQueryBuilder('e')
            ->innerJoin('e.categorie','c')
            ->where('e.categorie = :categorie')
            ->andWhere('e.medaille = :medaille')
            ->andWhere('e.procede = :procede')
            ->setParameter('categorie', $categorie)
            ->setParameter('medaille', $medaille)
            ->setParameter('procede', $procede)
            ->getQuery()
            ->getResult()
        ;
    }

     /**
    * @return Echantillon[] Returns an array of Echantillon objects
    */
    
    public function findEchConcoursForTables($concours)
    {
        return $this->createQueryBuilder('e')
            ->innerJoin('e.categorie','c')
            ->innerJoin('c.type','t','WITH','t.concours = :concours')
            ->where('c.participe = true')
            // ->innerJoin('e.user','u')
            // ->innerJoin('u.profil','p')
            ->setParameter('concours', $concours)
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
