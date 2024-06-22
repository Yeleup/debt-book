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
use App\Repository\PaymentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Get(),
        new Patch(),
        new Delete(),
    ],
    normalizationContext: ['groups' => ['payment.read']],
    denormalizationContext: ["groups" => ['payment.write']],
)]

#[ApiResource(
    uriTemplate: '/organizations/{organizationId}/payments',
    operations: [
        new GetCollection(),
        new Post(
            uriTemplate: '/organizations/{organizationId}/payments',
            provider: CreateProvider::class,
        ),
    ],
    uriVariables: [
        'organizationId' => new Link(
            toProperty: 'organization',
            fromClass: Organization::class
        )
    ],
    normalizationContext: ["groups" => ['payment.read']],
    denormalizationContext: ["groups" => ['payment.write']],
)]
#[Entity(repositoryClass: PaymentRepository::class)]
class Payment
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    #[Groups(['payment.read', 'transaction.read', 'customer.transaction.read'])]
    private ?int $id = null;

    #[Column(type: 'string', length: 255)]
    #[Groups(['payment.read', 'payment.write', 'transaction.read', 'customer.transaction.read'])]
    private ?string $title = null;

    #[OneToMany(targetEntity: Transaction::class, mappedBy: 'payment')]
    private Collection $transactions;

    #[ManyToOne(inversedBy: 'payments')]
    #[JoinColumn(nullable:false, onDelete: 'CASCADE')]
    private ?Organization $organization = null;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions[] = $transaction;
            $transaction->setPayment($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getPayment() === $this) {
                $transaction->setPayment(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->title;
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
