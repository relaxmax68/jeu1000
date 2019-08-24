<?php

namespace App\Repository;

use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[]    findAll()
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Question::class);
    }

    // /**
    //  * @return Question[] Returns an array of Question objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    public function findAllByLevel($level)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.level = :val')
            ->setParameter('val', $level)
            ->getQuery()
            ->getResult()
        ;
    }

    public function questionsNonPosees($level)
    {
        return $this->createQueryBuilder('q')
            ->Where('q.flag = false')
            ->andWhere('q.level = :val')
            ->setParameter('val', $level)
            ->getQuery()
            ->getResult()
        ;
    }

    public function questionsRestante()
    {
        return $this->createQueryBuilder('q')
            ->Where('q.flag = false')
            ->getQuery()
            ->getResult()
        ;
    }
}
