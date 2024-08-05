<?php

namespace App\Repository;

use App\Entity\Bank;
use App\Entity\Employee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Bank>
 */
class BankRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bank::class);
    }

    public function updateEmployeeTotal(Employee $employee): void
    {
        $entityManager = $this->getEntityManager();
        $totalExpenses = $this->createQueryBuilder('e')
            ->select('SUM(e.amount) as total')
            ->where('e.employee = :employee')
            ->setParameter('employee', $employee)
            ->getQuery()
            ->getSingleScalarResult();
        $employee->setTotal($totalExpenses);
        $entityManager->persist($employee);
        $entityManager->flush();
    }

//    /**
//     * @return Bank[] Returns an array of Bank objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Bank
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
