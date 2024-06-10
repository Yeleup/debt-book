<?php

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

use function Symfony\Component\String\u;

/**
 * @method Customer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customer[]    findAll()
 * @method Customer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    public function updateCustomerTotalAndLastTransaction(Customer $customer): void
    {
        $entityManager = $this->getEntityManager();
        $total = $entityManager->getRepository(Transaction::class)->sumAmountByCustomer($customer);
        $customer->setTotal($total);
        $customer->setLastTransaction(new \DateTime('now'));
        $entityManager->persist($customer);
        $entityManager->flush();
    }
}
