<?php

namespace App\Repository;

use App\Entity\Duo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Duo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Duo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Duo[]    findAll()
 * @method Duo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DuoRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Duo::class);
    }

    // /**
    //  * @return Duo[] Returns an array of Duo objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Duo
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
