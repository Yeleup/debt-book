<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Employee;
use Symfony\Bundle\SecurityBundle\Security;

class EmployeeStateProcessor implements ProcessorInterface
{
    public function __construct(
        protected ProcessorInterface $persistProcessor,
        protected ProcessorInterface $removeProcessor,
        protected Security $security,
    )
    {
    }

    /**
     * @param Employee $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $data->setUser($this->security->getUser());
        $data->setRole(Employee::ROLE_EMPLOYEE);
        $data->setStatus(false);
        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
