<?php

namespace App\Repository;

use App\Entity\Market;
use App\Entity\Payment;
use App\Entity\Type;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Payment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Payment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Payment[]    findAll()
 * @method Payment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    public function findPaymentsWithTransactionsSum(Type $type, Market $market, $startDate, $endDate)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select('p.id', 'p.title', 'SUM(transaction.amount) as amount')
            ->join('p.transactions', 'transaction')
            ->join('transaction.type', 'type')
            ->join('transaction.customer', 'customer')
            ->join('customer.market', 'market')
            ->where('type = :type')
            ->andWhere('market = :market')
            ->setParameter('type', $type)
            ->setParameter('market', $market);

        if ($startDate) {
            $qb->andWhere('transaction.createdAt > :startDate')->setParameter('startDate', $startDate);
        }

        if ($endDate) {
            $qb->andWhere('transaction.createdAt < :endDate')->setParameter('endDate', $endDate);
        }

        return $qb->groupBy('p.id')->getQuery()->getResult();
    }
}
