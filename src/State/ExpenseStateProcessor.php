<?php

namespace App\State;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Employee;
use App\Entity\Expense;
use App\Entity\User;
use App\Repository\EmployeeRepository;
use App\Repository\ExpenseRepository;
use Symfony\Bundle\SecurityBundle\Security;

class ExpenseStateProcessor implements ProcessorInterface
{

    public function __construct(
        protected ProcessorInterface $persistProcessor,
        protected ProcessorInterface $removeProcessor,
        protected Security $security,
        protected ExpenseRepository $expenseRepository,
        protected EmployeeRepository $employeeRepository,
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

            if ($operation instanceof Post) {
                /** @var Employee $employee */
                $employee = $this->employeeRepository->findOneBy(['user' => $this->security->getUser(), 'organization' => $data->getOrganization()]);
                if ($employee) {
                    $data->setEmployee($employee);
                }
            }
        }
        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
