<?php

namespace App\State;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Expense;
use App\Entity\User;
use App\Repository\ExpenseRepository;
use Symfony\Bundle\SecurityBundle\Security;

class ExpenseStateProcessor implements ProcessorInterface
{

    public function __construct(
        protected ProcessorInterface $persistProcessor,
        protected ProcessorInterface $removeProcessor,
        protected Security $security,
        protected ExpenseRepository $expenseRepository
    )
    {
    }

    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        /** @var User $currentUser */
        $currentUser = $this->security->getUser();
        if ($data instanceof Expense) {
            if ($operation instanceof DeleteOperationInterface) {
                return $this->removeProcessor->process($data, $operation, $uriVariables, $context);
            }

            $data->setUser($currentUser);
            $data = $this->expenseRepository->plusOrMinusDependingType($data, $currentUser);
            if ($data->getAssociatedUser()) {
                $newExpense = clone $data;
                $newExpense->setUser($data->getAssociatedUser());
                $newExpense->setAssociatedUser($this->security->getUser());
                $newExpense = $this->expenseRepository->plusOrMinusDependingType($newExpense, $currentUser);
                $this->persistProcessor->process($newExpense, $operation, $uriVariables, $context);
            }
        }
        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
