<?php

namespace App\Repository;

use App\Entity\Market;
use App\Entity\Type;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Type|null find($id, $lockMode = null, $lockVersion = null)
 * @method Type|null findOneBy(array $criteria, array $orderBy = null)
 * @method Type[]    findAll()
 * @method Type[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Type::class);
    }

    public function findTypesWithTransactionsSum(Market $market, $startDate = null, $endDate = null)
    {
        $qb = $this->createQueryBuilder('t');
        $qb->select('t.id', 't.title', 't.payment_status', 'SUM(transaction.amount) as amount')
            ->join('t.transactions', 'transaction')
            ->join('transaction.customer', 'customer')
            ->join('customer.market', 'market')
            ->where('market = :market')
            ->setParameter('market', $market);

        if ($startDate) {
            $qb->andWhere('transaction.createdAt > :startDate')->setParameter('startDate', $startDate);
        }

        if ($endDate) {
            $qb->andWhere('transaction.createdAt < :endDate')->setParameter('endDate', $endDate);
        }

        return $qb->groupBy('t.id')->getQuery()->getResult();
    }
}
