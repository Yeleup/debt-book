<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\CreateProvider;
use App\Repository\TransferRepository;
use App\State\TransferStateProcessor;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TransferRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new Patch(),
        new Delete(),
    ],
    normalizationContext: ["groups" => ["transfer.read"]],
    denormalizationContext: ["groups" => ["transfer.write"]],
)]
#[ApiResource(
    uriTemplate: '/employees/{employeeId}/transfers',
    operations: [new GetCollection()],
    uriVariables: [
        'employeeId' => new Link(toProperty: 'employee', fromClass: Employee::class),
    ],
    normalizationContext: ['groups' => ['employee:transfers']],
)]
#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/organizations/{organizationId}/transfers',
            provider: CreateProvider::class,
        ),
    ],
    uriVariables: [
        'organizationId' => new Link(
            toProperty: 'organization',
            fromClass: Organization::class
        )
    ],
    normalizationContext: ["groups" => ['transfer.read']],
    denormalizationContext: ["groups" => ['transfer.write']],
    processor: TransferStateProcessor::class,
)]
#[ORM\HasLifecycleCallbacks]
class Transfer
{
    use Traits\TimestampableTrait;
    use Traits\GenerateReferenceTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['transfer.read', 'employee:transfers'])]
    private ?int $id = null;

    #[ManyToOne(targetEntity: Organization::class)]
    #[JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Groups(['transfer.read'])]
    private ?Organization $organization = null;

    #[ORM\ManyToOne(inversedBy: 'transfers')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['transfer.read', 'employee:transfers'])]
    private ?Employee $employee = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['transfer.read', 'transfer.write', 'employee:transfers'])]
    private ?Employee $receiverEmployee = null;

    #[ORM\Column]
    #[Groups(['transfer.read', 'transfer.write', 'employee:transfers'])]
    private ?float $amount = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['transfer.read', 'transfer.write', 'employee:transfers'])]
    private ?string $comment = null;

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

    public function getReceiverEmployee(): ?Employee
    {
        return $this->receiverEmployee;
    }

    public function setReceiverEmployee(?Employee $receiverEmployee): static
    {
        $this->receiverEmployee = $receiverEmployee;

        return $this;
    }
}
