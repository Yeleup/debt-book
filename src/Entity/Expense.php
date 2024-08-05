<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\CreateProvider;
use App\Repository\ExpenseRepository;
use App\State\ExpenseStateProcessor;
use App\State\TransferStateProcessor;
use App\Validator\User\IsRoleControl;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ExpenseRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new Patch(),
        new Delete(),
    ],
    normalizationContext: ["groups" => ["expense.read"]],
    denormalizationContext: ["groups" => ["expense.write"]],
    order: ['createdAt' => 'DESC'],
    processor: ExpenseStateProcessor::class,
)]
#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/organizations/{organizationId}/expenses',
            provider: CreateProvider::class,
        ),
    ],
    uriVariables: [
        'organizationId' => new Link(
            toProperty: 'organization',
            fromClass: Organization::class
        )
    ],
    normalizationContext: ["groups" => ['expense.read']],
    denormalizationContext: ["groups" => ['expense.write']],
    processor: ExpenseStateProcessor::class,
)]
#[ApiResource(
    uriTemplate: '/employees/{employeeId}/expenses',
    operations: [new GetCollection()],
    uriVariables: [
        'employeeId' => new Link(toProperty: 'employee', fromClass: Employee::class),
    ],
    normalizationContext: ['groups' => ['employee.read']],
)]
#[ApiFilter(DateFilter::class, properties: ["createdAt"])]
#[ApiFilter(OrderFilter::class, properties: ["createdAt"])]
#[ORM\HasLifecycleCallbacks]
class Expense
{
    use Traits\TimestampableTrait;
    use Traits\GenerateReferenceTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[ApiProperty(identifier: true)]
    #[Groups(groups: ['expense.read', 'user.expense.read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::FLOAT, precision: 10, scale: 0)]
    #[Groups(groups: ['expense.read', 'expense.write', 'user.expense.read'])]
    private ?float $amount = null;

    #[ORM\ManyToOne(inversedBy: 'expenses')]
    private ?Employee $employee = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(groups: ['expense.read', 'expense.write', 'user.expense.read'])]
    private ?string $comment = null;

    #[ORM\ManyToOne(inversedBy: 'expenses')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(groups: ['expense.read', 'expense.write', 'user.expense.read'])]
    private ?ExpenseType $expenseType = null;

    #[ORM\ManyToOne]
    private ?Organization $organization = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getExpenseType(): ?ExpenseType
    {
        return $this->expenseType;
    }

    public function setExpenseType(?ExpenseType $expenseType): static
    {
        $this->expenseType = $expenseType;

        return $this;
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function setEmployee(?Employee $employee): static
    {
        $this->employee = $employee;

        return $this;
    }

    public function getOrganization(): ?Organization
    {
        return $this->organization;
    }

    public function setOrganization(?Organization $organization): static
    {
        $this->organization = $organization;

        return $this;
    }
}
