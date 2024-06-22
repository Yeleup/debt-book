<?php

namespace App\State;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Transaction;
use App\Repository\TransactionRepository;
use Symfony\Bundle\SecurityBundle\Security;

class TransactionStateProcessor implements ProcessorInterface
{

    public function __construct(
        protected ProcessorInterface $persistProcessor,
        protected ProcessorInterface $removeProcessor,
        protected Security $security,
        protected TransactionRepository $transactionRepository
    )
    {
    }

    /**
     * @param Transaction $data
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     * @return mixed
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($operation instanceof DeleteOperationInterface) {
            return $this->removeProcessor->process($data, $operation, $uriVariables, $context);
        }
        $data = $this->transactionRepository->plusOrMinusDependingType($data);
        $data->setUser($this->security->getUser());
        $data->setOrganization($data->getCustomer()->getMarket()->getOrganization());
        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
