<?php

namespace App\EventListener;

use App\Entity\Customer;
use App\Entity\Expense;
use App\Entity\Organization;
use App\Entity\Transaction;
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
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Organization) {
            $this->entityManager->getRepository(Organization::class)->generateUniqueCode($entity);
        }
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Transaction) {
            $customer = $entity->getCustomer();
            $this->entityManager->getRepository(Customer::class)->updateCustomerTotalAndLastTransaction($customer);
        }

        if ($entity instanceof Expense) {
            $user = $entity->getUser();
            $this->entityManager->getRepository(Expense::class)->updateUserExpenseTotal($user);
        }
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Transaction) {
            $customer = $entity->getCustomer();
            $this->entityManager->getRepository(Customer::class)->updateCustomerTotalAndLastTransaction($customer);
        }

        if ($entity instanceof Expense) {
            $user = $entity->getUser();
            $this->entityManager->getRepository(Expense::class)->updateUserExpenseTotal($user);
        }
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Transaction) {
            $customer = $entity->getCustomer();
            $this->entityManager->getRepository(Customer::class)->updateCustomerTotalAndLastTransaction($customer);
        }

        if ($entity instanceof Expense) {
            $user = $entity->getUser();
            $this->entityManager->getRepository(Expense::class)->updateUserExpenseTotal($user);
        }
    }
}