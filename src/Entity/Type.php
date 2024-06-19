<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\CreateProvider;
use App\Repository\TypeRepository;
use App\State\MarketStateProcessor;
use App\State\TypeStateProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;

#[ApiResource(
    operations: [
        new Get(),
        new Patch(),
        new Delete(),
    ],
    normalizationContext: ['groups' => ['type.read']],
    denormalizationContext: ['groups' => ['type.write']],
    paginationEnabled: false,
    processor: TypeStateProcessor::class
)]
#[ApiResource(
    uriTemplate: '/organizations/{organizationId}/types',
    operations: [
        new GetCollection(),
        new Post(
            uriTemplate: '/organizations/{organizationId}/types',
            provider: CreateProvider::class,
        ),
    ],
    uriVariables: [
        'organizationId' => new Link(
            toProperty: 'organization',
            fromClass: Organization::class
        )
    ],
    normalizationContext: ["groups" => ['type.read']],
    denormalizationContext: ["groups" => ['type.write']],
)]
#[ApiFilter(OrderFilter::class, properties: ['sort'])]
#[ORM\Entity(repositoryClass: TypeRepository::class)]
class Type
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['type.read', 'transaction.read', 'customer.transaction.read'])]
    private $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['type.read', 'transaction.read','type.write', 'customer.transaction.read'])]
    private ?string $title = null;

    #[ORM\OneToMany(targetEntity: Transaction::class, mappedBy: 'type')]
    private Collection $transactions;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['type.read', 'type.write'])]
    private bool $paymentStatus;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['type.read', 'type.write'])]
    private ?int $sort = null;

    #[ORM\ManyToOne(inversedBy: 'types')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Organization $organization = null;

    #[ORM\Column('is_add_amount')]
    #[Groups(['type.read', 'type.write'])]
    private ?bool $addAmountToCustomer = null;

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
            $transaction->setType($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getType() === $this) {
                $transaction->setType(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->title;
    }

    public function getPaymentStatus(): ?bool
    {
        return $this->paymentStatus;
    }

    public function setPaymentStatus(bool $paymentStatus): self
    {
        $this->paymentStatus = $paymentStatus;

        return $this;
    }

    public function getSort(): ?int
    {
        return $this->sort;
    }

    public function setSort(?int $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    public function isPaymentStatus(): ?bool
    {
        return $this->paymentStatus;
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

    public function isAddAmountToCustomer(): ?bool
    {
        return $this->addAmountToCustomer;
    }

    public function setAddAmountToCustomer(bool $addAmountToCustomer): static
    {
        $this->addAmountToCustomer = $addAmountToCustomer;

        return $this;
    }
}
