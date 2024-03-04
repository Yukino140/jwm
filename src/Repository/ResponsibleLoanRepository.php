<?php

namespace App\Repository;

use App\Entity\ResponsibleLoan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ResponsibleLoan>
 *
 * @method ResponsibleLoan|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResponsibleLoan|null findOneBy(array $criteria, array $orderBy = null)
 * @method ResponsibleLoan[]    findAll()
 * @method ResponsibleLoan[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResponsibleLoanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResponsibleLoan::class);
    }

//    /**
//     * @return ResponsibleLoan[] Returns an array of ResponsibleLoan objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ResponsibleLoan
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
