<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use App\Repository\BankRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BankRepository::class)]
#[ApiResource(
    uriTemplate: '/employees/{employeeId}/banks',
    operations: [new GetCollection()],
    uriVariables: [
        'organizationId' => new Link(toProperty: 'employee', fromClass: Employee::class),
    ],
    normalizationContext: ['groups' => ['employee.read']],
)]
#[ORM\HasLifecycleCallbacks]
class Bank
{
    use Traits\TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['bank.read'])]
    private ?string $reference = null;

    #[ManyToOne(targetEntity: Organization::class)]
    #[JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Groups(['bank.read'])]
    private ?Organization $organization = null;

    #[ORM\ManyToOne(inversedBy: 'banks')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['bank.read'])]
    private ?Employee $employee = null;

    #[ORM\Column]
    #[Groups(['bank.read'])]
    private ?float $amount = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['bank.read'])]
    private ?string $comment = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
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

    public function getOrganization(): ?Organization
    {
        return $this->organization;
    }

    public function setOrganization(?Organization $organization): static
    {
        $this->organization = $organization;

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
}
