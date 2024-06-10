<?php

namespace App\Repository;

use App\Entity\Organization;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Random\RandomException;

/**
 * @extends ServiceEntityRepository<Organization>
 */
class OrganizationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Organization::class);
    }

    public function generateUniqueCode(Organization $organization): Organization
    {
        do {
            // Генерация случайного 4-значного кода
            $code = strtoupper(bin2hex(random_bytes(2)));
            $existingOrganization = $this->findOneBy(['code' => $code]);
        } while ($existingOrganization !== null);
        $organization->setCode($code);
        return $organization;
    }
}
