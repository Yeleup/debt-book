<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\OrganizationRepository;
use App\State\OrganizationStateProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: OrganizationRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new Patch(),
        new Delete(),
        new Post(),
        new GetCollection()
    ],
    normalizationContext: ['groups' => ['organization.read']],
    denormalizationContext: ['groups' => ['organization.write']],
    processor: OrganizationStateProcessor::class
)]
#[ORM\HasLifecycleCallbacks]
#[ApiFilter(SearchFilter::class, properties: ['code' => 'exact'])]
#[ApiFilter(BooleanFilter::class, properties: ['employees.status'])]
class Organization
{
    use Traits\TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['organization.read', 'user.me'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['organization.read', 'organization.write', 'user.me'])]
    private ?string $title = null;

    #[ORM\Column(length: 6, nullable: true)]
    #[Groups(['organization.read', 'organization.write'])]
    private ?string $code = null;

    /**
     * @var Collection<int, Employee>
     */
    #[ORM\OneToMany(targetEntity: Employee::class, mappedBy: 'organization', cascade: ['persist'], orphanRemoval: true)]
    private Collection $employees;

    /**
     * @var Collection<int, Market>
     */
    #[ORM\OneToMany(targetEntity: Market::class, mappedBy: 'organization', cascade: ['persist'])]
    #[Link(toProperty: 'organization')]
    private Collection $markets;

    /**
     * @var Collection<int, Payment>
     */
    #[ORM\OneToMany(targetEntity: Payment::class, mappedBy: 'organization', cascade: ['persist'], orphanRemoval: true)]
    #[Link(toProperty: 'organization')]
    private Collection $payments;

    public function __construct()
    {
        $this->employees = new ArrayCollection();
        $this->markets = new ArrayCollection();
        $this->payments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): static
    {
        $this->code = $code;

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
            $employee->setOrganization($this);
        }

        return $this;
    }

    public function removeEmployee(Employee $employee): static
    {
        if ($this->employees->removeElement($employee)) {
            // set the owning side to null (unless already changed)
            if ($employee->getOrganization() === $this) {
                $employee->setOrganization(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Market>
     */
    public function getMarkets(): Collection
    {
        return $this->markets;
    }

    public function addMarket(Market $market): static
    {
        if (!$this->markets->contains($market)) {
            $this->markets->add($market);
            $market->setOrganization($this);
        }

        return $this;
    }

    public function removeMarket(Market $market): static
    {
        if ($this->markets->removeElement($market)) {
            // set the owning side to null (unless already changed)
            if ($market->getOrganization() === $this) {
                $market->setOrganization(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Payment>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): static
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setOrganization($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): static
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getOrganization() === $this) {
                $payment->setOrganization(null);
            }
        }

        return $this;
    }
}
