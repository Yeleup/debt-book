<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\EmployeeRepository;
use App\State\EmployeeStateProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToMany;
use Symfony\Component\Serializer\Attribute\Groups;
use App\Validator\Employee\UniqueEmployee;

#[ApiResource(
    shortName: 'Apply',
    operations: [
        new Post(),
        new Delete()
    ],
    normalizationContext: ['groups' => ['user_organization.read']],
    denormalizationContext: ['groups' => ['user_organization.write']],
    processor: EmployeeStateProcessor::class
)]
#[ApiResource(
    uriTemplate: '/organizations/{organizationId}/employees',
    shortName: 'Employee',
    operations: [new GetCollection()],
    uriVariables: [
        'organizationId' => new Link(toProperty: 'organization', fromClass: Organization::class),
    ],
    normalizationContext: ['groups' => ['user_organization.read']],
    paginationItemsPerPage: 10
)]
#[ApiResource(
    uriTemplate: '/employees/{id}',
    shortName: 'Employee',
    operations: [
        new Get(),
        new Patch()
    ],
)]
#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
#[ORM\UniqueConstraint(name: 'user_organization_unique', fields: ['user', 'organization'])]
//#[UniqueEmployee]
class Employee
{
    const ROLE_OWNER = 'owner';
    const ROLE_ADMIN = 'admin';
    const ROLE_EMPLOYEE = 'employee';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user_organization.read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'employees')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['user_organization.read', 'transaction.read'])]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'employees')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Groups(['user_organization.read', 'user_organization.write', 'user.me'])]
    private ?Organization $organization = null;

    #[ORM\Column(length: 50)]
    #[Groups(['user_organization.read'])]
    private ?string $role = null;

    #[ORM\Column]
    #[Groups(['user_organization.read'])]
    private ?bool $status = null;

    /**
     * @var Collection<int, Market>
     */
    #[ORM\ManyToMany(targetEntity: Market::class, inversedBy: 'employees')]
    #[Groups(['user_organization.read'])]
    private Collection $markets;

    /**
     * @var Collection<int, Transfer>
     */
    #[ORM\OneToMany(targetEntity: Transfer::class, mappedBy: 'employee', orphanRemoval: true)]
    private Collection $transfers;

    /**
     * @var Collection<int, Bank>
     */
    #[ORM\OneToMany(targetEntity: Bank::class, mappedBy: 'employee', orphanRemoval: true)]
    private Collection $banks;

    #[ORM\Column(nullable: true)]
    #[Groups(['user.read', 'user.me', 'user_organization.read'])]
    private ?float $total = null;

    /**
     * @var Collection<int, Expense>
     */
    #[ORM\OneToMany(targetEntity: Expense::class, mappedBy: 'employee')]
    private Collection $expenses;

    public function __construct()
    {
        $this->markets = new ArrayCollection();
        $this->transfers = new ArrayCollection();
        $this->banks = new ArrayCollection();
        $this->expenses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

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

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): static
    {
        $this->status = $status;

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
        }

        return $this;
    }

    public function removeMarket(Market $market): static
    {
        $this->markets->removeElement($market);

        return $this;
    }

    /**
     * @return Collection<int, Transfer>
     */
    public function getTransfers(): Collection
    {
        return $this->transfers;
    }

    public function addTransfer(Transfer $transfer): static
    {
        if (!$this->transfers->contains($transfer)) {
            $this->transfers->add($transfer);
            $transfer->setEmployee($this);
        }

        return $this;
    }

    public function removeTransfer(Transfer $transfer): static
    {
        if ($this->transfers->removeElement($transfer)) {
            // set the owning side to null (unless already changed)
            if ($transfer->getEmployee() === $this) {
                $transfer->setEmployee(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Bank>
     */
    public function getBanks(): Collection
    {
        return $this->banks;
    }

    public function addBank(Bank $bank): static
    {
        if (!$this->banks->contains($bank)) {
            $this->banks->add($bank);
            $bank->setEmployee($this);
        }

        return $this;
    }

    public function removeBank(Bank $bank): static
    {
        if ($this->banks->removeElement($bank)) {
            // set the owning side to null (unless already changed)
            if ($bank->getEmployee() === $this) {
                $bank->setEmployee(null);
            }
        }

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(?float $total): static
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @return Collection<int, Expense>
     */
    public function getExpenses(): Collection
    {
        return $this->expenses;
    }

    public function addExpense(Expense $expense): static
    {
        if (!$this->expenses->contains($expense)) {
            $this->expenses->add($expense);
            $expense->setEmployee($this);
        }

        return $this;
    }

    public function removeExpense(Expense $expense): static
    {
        if ($this->expenses->removeElement($expense)) {
            // set the owning side to null (unless already changed)
            if ($expense->getEmployee() === $this) {
                $expense->setEmployee(null);
            }
        }

        return $this;
    }
}
