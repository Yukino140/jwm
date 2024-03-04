<?php

namespace App\Repository;

use App\Entity\ResponsibleInvestment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ResponsibleInvestment>
 *
 * @method ResponsibleInvestment|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResponsibleInvestment|null findOneBy(array $criteria, array $orderBy = null)
 * @method ResponsibleInvestment[]    findAll()
 * @method ResponsibleInvestment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResponsibleInvestmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResponsibleInvestment::class);
    }

//    /**
//     * @return ResponsibleInvestment[] Returns an array of ResponsibleInvestment objects
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

//    public function findOneBySomeField($value): ?ResponsibleInvestment
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
