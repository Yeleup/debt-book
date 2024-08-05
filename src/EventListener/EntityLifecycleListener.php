<?php

namespace App\EventListener;

use App\Entity\Bank;
use App\Entity\Customer;
use App\Entity\Expense;
use App\Entity\Organization;
use App\Entity\Transaction;
use App\Entity\Transfer;
use App\Repository\BankRepository;
use App\Repository\CustomerRepository;
use App\Repository\ExpenseRepository;
use App\Repository\OrganizationRepository;
use App\Repository\TransferRepository;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

#[AsDoctrineListener(Events::prePersist)]
#[AsDoctrineListener(Events::postPersist)]
#[AsDoctrineListener(Events::postUpdate)]
#[AsDoctrineListener(Events::postRemove)]
class EntityLifecycleListener
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected OrganizationRepository $organizationRepository,
        protected CustomerRepository $customerRepository,
        protected ExpenseRepository $expenseRepository,
        protected TransferRepository $transferRepository,
        protected BankRepository $bankRepository,
    )
    {
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Organization) {
            $this->organizationRepository->generateUniqueCode($entity);
        }
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Transaction) {
            $customer = $entity->getCustomer();
            $this->customerRepository->updateCustomerTotalAndLastTransaction($customer);
        }

        if ($entity instanceof Expense) {
            $user = $entity->getUser();
            $this->expenseRepository->updateUserExpenseTotal($user);
        }

        if ($entity instanceof Transfer) {
            $bank = new Bank();
            $bank->setReference($entity->getReference());
            $bank->setAmount($entity->getAmount());
            $bank->setEmployee($entity->getReceiverEmployee());
            $bank->setOrganization($entity->getOrganization());
            $bank->setComment($entity->getComment());
            $this->entityManager->persist($bank);
            $this->entityManager->flush();
        }

        if ($entity instanceof Bank) {
            $this->bankRepository->updateEmployeeTotal($entity->getEmployee());
        }
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Transaction) {
            $customer = $entity->getCustomer();
            if ($customer) $this->customerRepository->updateCustomerTotalAndLastTransaction($customer);
        }

        if ($entity instanceof Expense) {
            $user = $entity->getUser();
            $this->expenseRepository->updateUserExpenseTotal($user);
        }

        if ($entity instanceof Transfer) {
            $bank = $this->bankRepository->findOneBy(['reference' => $entity->getReference()]);
            $this->bankRepository->updateEmployeeTotal($bank->getEmployee());
        }
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Transaction) {
            $customer = $entity->getCustomer();
            if ($customer) $this->customerRepository->updateCustomerTotalAndLastTransaction($customer);
        }

        if ($entity instanceof Expense) {
            $user = $entity->getUser();
            if ($user) $this->expenseRepository->updateUserExpenseTotal($user);
        }

        if ($entity instanceof Transfer) {
            $bank = $this->bankRepository->findOneBy(['reference' => $entity->getReference()]);
            if ($bank) {
                $this->entityManager->remove($bank);
                $this->entityManager->flush();
            }
        }

        if ($entity instanceof Bank) {
            if ($entity->getEmployee()) $this->bankRepository->updateEmployeeTotal($entity->getEmployee());
        }
    }
}