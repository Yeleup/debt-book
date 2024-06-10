<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Organization;
use App\Entity\UserOrganization;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class OrganizationStateProcessor implements ProcessorInterface
{
    public function __construct(
        protected ProcessorInterface $persistProcessor,
        protected ProcessorInterface $removeProcessor,
        protected Security $security,
    )
    {
    }

    /**
     * @param Organization $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $userOrganization = new UserOrganization();
        $userOrganization->setUser($this->security->getUser());
        $userOrganization->setOrganization($data);
        $userOrganization->setRole(UserOrganization::ROLE_OWNER);
        $data->addUserOrganization($userOrganization);
        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
