<?php

namespace App\Repository;

use App\Form\RegisterUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method RegisterUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method RegisterUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method RegisterUser[]    findAll()
 * @method RegisterUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RegisterUserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, RegisterUser::class);
    }

//    /**
//     * @return RegisterUser[] Returns an array of RegisterUser objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RegisterUser
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
