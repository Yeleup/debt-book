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
use App\Repository\MarketRepository;
use App\State\MarketStateProcessor;
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
    normalizationContext: ['groups' => ['market.read']],
    denormalizationContext: ['groups' => ['market.write']],
    paginationEnabled: false,
    processor: MarketStateProcessor::class,
)]
#[ApiResource(
    uriTemplate: '/organizations/{organizationId}/markets',
    operations: [
        new GetCollection(),
        new Post(
            uriTemplate: '/organizations/{organizationId}/markets',
            provider: CreateProvider::class,
        ),
    ],
    uriVariables: [
        'organizationId' => new Link(
            toProperty: 'organization',
            fromClass: Organization::class
        )
    ],
    normalizationContext: ["groups" => ['market.read']],
    denormalizationContext: ["groups" => ['market.write']],
    paginationItemsPerPage: 10,
    processor: MarketStateProcessor::class,
)]
#[Entity(repositoryClass: MarketRepository::class)]
class Market
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    #[Groups(['market.read', 'user.me'])]
    private ?int $id = null;

    #[Column(type: 'string', length: 255)]
    #[Groups(['market.read', 'market.write', 'user.me'])]
    private ?string $title = null;

    #[OneToMany(targetEntity: Customer::class, mappedBy: 'market')]
    private Collection $customers;

    #[ManyToOne(targetEntity: Organization::class, inversedBy: 'markets')]
    #[Groups(['market.read', 'user.me'])]
    private ?Organization $organization = null;

    /**
     * @var Collection<int, Employee>
     */
    #[ManyToMany(targetEntity: Employee::class, mappedBy: 'markets')]
    private Collection $employees;

    public function __construct()
    {
        $this->customers = new ArrayCollection();
        $this->employees = new ArrayCollection();
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

    public function __toString()
    {
        // TODO: Implement __toString() method.
        return $this->title;
    }

    /**
     * @return Collection|Customer[]
     */
    public function getCustomers(): Collection
    {
        return $this->customers;
    }

    public function addCustomer(Customer $customer): self
    {
        if (!$this->customers->contains($customer)) {
            $this->customers[] = $customer;
            $customer->setMarket($this);
        }

        return $this;
    }

    public function removeCustomer(Customer $customer): self
    {
        if ($this->customers->removeElement($customer)) {
            // set the owning side to null (unless already changed)
            if ($customer->getMarket() === $this) {
                $customer->setMarket(null);
            }
        }

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

    /**
     * @return Collection<int, Employee>
     */
    public function getEmployees(): Collection
    {
        return $this->employees;
    }

    public function addEmployee(Employee $employee): static
    {
        if (!$this->employees->contains($employee)) {
            $this->employees->add($employee);
            $employee->addMarket($this);
        }

        return $this;
    }

    public function removeEmployee(Employee $employee): static
    {
        if ($this->employees->removeElement($employee)) {
            $employee->removeMarket($this);
        }

        return $this;
    }
}
