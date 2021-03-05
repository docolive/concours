<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
    * @return User[] Returns an array of User objects
    */
    public function findJures($concoursId)
    {
        return $this->createQueryBuilder('u')
            ->innerJoin('u.profil','p','WITH','p.jure = true')
            ->leftJoin('u.echantillons','e')
            ->leftJoin('e.categorie','c')
            ->orderBy('p.nom', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
    * @return User[] Returns an array of User objects
    */
    public function findCandidats($concours)
    {
        return $this->createQueryBuilder('u')
            ->innerJoin('u.profil','p')
            ->innerJoin('u.echantillons','e')
            ->innerJoin('e.categorie','c')
            ->innerJoin('c.type','t','WITH','t.concours = :concours')
            ->orderBy('p.nom', 'ASC')
            ->setParameter('concours', $concours)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
    * @return User[] Returns an array of User objects
    */
    public function findCandidatsPourAdmin()
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.email', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

/**
    * @return User[] Returns an array of User objects
    */
    public function findByRoleAdmin()
    {
        return $this->createQueryBuilder('u')
            ->Where('u.roles = :role')
            ->orWhere('u.roles = :roleSup')
            ->setParameter('role', '["ROLE_ADMIN"]')
            ->setParameter('roleSup', '["ROLE_SUPER_ADMIN"]')
            ->getQuery()
            ->getResult();
    }
    

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
