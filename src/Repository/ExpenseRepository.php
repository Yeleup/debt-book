<?php

namespace App\Repository;

use App\Entity\Expense;
use App\Entity\ExpenseType;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Expense>
 *
 * @method Expense|null find($id, $lockMode = null, $lockVersion = null)
 * @method Expense|null findOneBy(array $criteria, array $orderBy = null)
 * @method Expense[]    findAll()
 * @method Expense[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExpenseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Expense::class);
    }

    public function sumByExpenseTypeAndDateRange(ExpenseType $expenseType, ?string $startDate, ?string $endDate): ?float
    {
        $qb = $this->createQueryBuilder('e')
            ->select('SUM(e.amount) as total')
            ->where('e.expenseType = :expenseType')
            ->setParameter('expenseType', $expenseType);

        if ($startDate) {
            $qb->andWhere('e.createdAt > :startDate')->setParameter('startDate', $startDate);
        }
        if ($endDate) {
            $qb->andWhere('e.createdAt < :endDate')->setParameter('endDate', $endDate);
        }
        return $qb->getQuery()->getSingleScalarResult();
    }
}
