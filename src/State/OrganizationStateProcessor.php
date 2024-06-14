<?php

namespace App\State;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
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
        if ($operation instanceof DeleteOperationInterface) {
            return $this->removeProcessor->process($data, $operation, $uriVariables, $context);
        } elseif ($operation instanceof Post) {
            $userOrganization = new UserOrganization();
            $userOrganization->setUser($this->security->getUser());
            $userOrganization->setOrganization($data);
            $userOrganization->setRole(UserOrganization::ROLE_OWNER);
            $userOrganization->setStatus(true);
            $data->addUserOrganization($userOrganization);
        }
        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
