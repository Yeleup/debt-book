<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\UserOrganization;
use Symfony\Bundle\SecurityBundle\Security;

class UserOrganizationStateProcessor implements ProcessorInterface
{
    public function __construct(
        protected ProcessorInterface $persistProcessor,
        protected ProcessorInterface $removeProcessor,
        protected Security $security,
    )
    {
    }

    /**
     * @param UserOrganization $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $data->setUser($this->security->getUser());
        $data->setRole(UserOrganization::ROLE_MEMBER);
        $data->setStatus(false);


        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
