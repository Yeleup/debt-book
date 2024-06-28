<?php

namespace App\ApiPlatform;
use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Organization;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;

class OrganizationsByUserExtension implements QueryCollectionExtensionInterface
{
    public function __construct(protected Security $security)
    {
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        if ($resourceClass !== Organization::class) {
            return;
        }

        $code = Request::createFromGlobals()->query->get('code');
        if ($code) {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];

        if ($this->security->getUser()) {
            $queryBuilder
                ->innerJoin(sprintf('%s.employees', $alias), 'e', 'WITH', 'e.user = :user')
                ->setParameter('user', $this->security->getUser());
        }
    }
}