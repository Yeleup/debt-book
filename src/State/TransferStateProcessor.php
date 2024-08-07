<?php

namespace App\State;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Employee;
use App\Entity\Transfer;
use App\Repository\EmployeeRepository;
use Symfony\Bundle\SecurityBundle\Security;

class TransferStateProcessor implements ProcessorInterface
{
    public function __construct(
        protected ProcessorInterface $persistProcessor,
        protected ProcessorInterface $removeProcessor,
        protected Security $security,
        protected EmployeeRepository $employeeRepository,
    )
    {
    }

    /**
     * @param Transfer $data
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     * @return mixed
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($operation instanceof DeleteOperationInterface) {
            return $this->removeProcessor->process($data, $operation, $uriVariables, $context);
        }

        if ($operation instanceof Post) {
            /** @var Employee $employee */
            $employee = $this->employeeRepository->findOneBy(['user' => $this->security->getUser(), 'organization' => $data->getOrganization()]);
            if ($employee) {
                $data->setEmployee($employee);
            }
        }
        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
