<?php

namespace App\Validator\Employee;

use App\Entity\Employee;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueEmployeeValidator extends ConstraintValidator
{
    private EntityManagerInterface $entityManager;
    private Security $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        /* @var UniqueEmployee $constraint */

        if (!$value instanceof Employee) {
            return;
        }

        $user = $this->security->getUser();
        if (!$user) {
            return;
        }

        $existingRequest = $this->entityManager->getRepository(Employee::class)->findOneBy([
            'user' => $user,
            'organization' => $value->getOrganization(),
        ]);

        if ($existingRequest) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $user->getUserIdentifier())
                ->addViolation();
        }
    }
}
