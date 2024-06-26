<?php

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function sumAmountByCustomer(Customer $customer): float
    {
        $qb = $this->createQueryBuilder('co');

        return $qb->select('SUM(co.amount)')
            ->where('co.customer = :customer')
            ->setParameter('customer', $customer)
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
    }

    public function plusOrMinusDependingType(Transaction $transaction): Transaction
    {
        if ($transaction->getType()) {
            $amount = (float) abs($transaction->getAmount());

            // Плюсуем или минусуем, смотря по префиксу
            if ($transaction->getType()->isAddAmountToCustomer()) {
                $amount = -1 * $amount;
                $transaction->setAmount($amount);
            } else {
                $transaction->setAmount($amount);
            }

            // Не показываем оплату если в типах не указано
            if (!$transaction->getType()->getPaymentStatus()) {
                $transaction->setPayment(null);
            }
        }

        return $transaction;
    }
}
