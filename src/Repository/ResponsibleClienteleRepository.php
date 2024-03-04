<?php

namespace App\Repository;

use App\Entity\ResponsibleClientele;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ResponsibleClientele>
 *
 * @method ResponsibleClientele|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResponsibleClientele|null findOneBy(array $criteria, array $orderBy = null)
 * @method ResponsibleClientele[]    findAll()
 * @method ResponsibleClientele[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResponsibleClienteleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResponsibleClientele::class);
    }

//    /**
//     * @return ResponsibleClientele[] Returns an array of ResponsibleClientele objects
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

//    public function findOneBySomeField($value): ?ResponsibleClientele
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
