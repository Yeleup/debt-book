<?php

namespace App\ApiPlatform;
use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Customer;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

class CustomersByUserMarketsExtension implements QueryCollectionExtensionInterface
{
    private Security $security;
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        if ($resourceClass !== Customer::class) {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];

        if ($this->security->getUser()) {
            $queryBuilder
                ->join(sprintf('%s.market', $alias), 'm')
                ->join('m.employees', 'employee')
                ->where('employee.user = :currentUser')
                ->setParameter('currentUser', $this->security->getUser());
        }
    }
}