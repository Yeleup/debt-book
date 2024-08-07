<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Dto\UserMeResetPasswordDto;
use App\Repository\UserRepository;
use App\State\UserMeResetPasswordStateProcessor;
use App\State\UserMeStateProvider;
use App\State\UserStateProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToMany;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/users/me',
            status: 200,
            normalizationContext: ['groups' => 'user.me'],
            output: User::class,
            name: 'get_me',
            provider: UserMeStateProvider::class,
        ),
        new Get(),
        new Post(),
        new Patch(),
        new Delete(),
        new Post(
            uriTemplate: '/users/me/change-password',
            status: 202,
            input: UserMeResetPasswordDto::class,
            processor: UserMeResetPasswordStateProcessor::class,
        ),
    ],
    normalizationContext: ["groups" => ["user.read"]],
    denormalizationContext: ["groups" => ["user.write"]],
    processor: UserStateProcessor::class
)]
#[ApiResource(
    uriTemplate: '/organizations/{organizationId}/users',
    operations: [
        new GetCollection(),
    ],
    uriVariables: [
        'organizationId' => new Link(
            toProperty: 'organization',
            fromClass: Organization::class
        )
    ],
    normalizationContext: ["groups" => ["user.read"]],
    denormalizationContext: ["groups" => ["user.write"]],
    paginationItemsPerPage: 10,
)]

#[Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['username'], message: 'This username is already taken.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    #[Groups(['user.read', 'user.me', 'user_organization.read'])]
    private ?int $id = null;

    #[Column(type: 'string', length: 180, unique: true, nullable: true)]
    #[Groups(['user.read', 'user.write', 'user.me', 'user_organization.read'])]
    private string $username;

    #[Column(type: 'json')]
    #[Groups(['user.read', 'user.write', 'user.me'])]
    private array $roles = [];

    #[Column(type: 'string')]
    #[Groups(['user.write'])]
    private string $password;

    #[Column(name: 'full_name', type: 'string', length: 180, nullable: true)]
    #[Groups(['transaction.read', 'user.read', 'user.write', 'user.me', 'user_organization.read'])]
    private ?string $fullName = null;

    /**
     * @var Collection<int, Employee>
     */
    #[ORM\OneToMany(targetEntity: Employee::class, mappedBy: 'user')]
    #[Groups(['user.read', 'user.me'])]
    private Collection $employees;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user.read', 'user.me', 'user_organization.read'])]
    private ?string $image = null;

    public function __construct()
    {
        $this->employees = new ArrayCollection();
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $password): void
    {
        $this->plainPassword = $password;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        // $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function __toString(): string
    {
        return $this->username;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    public function getFullName(): ?string
    {
        return (string) $this->fullName;
    }

    public function setFullName(?string $fullName): self
    {
        $this->fullName = $fullName;

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
            $employee->setUser($this);
        }

        return $this;
    }

    public function removeEmployee(Employee $employee): static
    {
        if ($this->employees->removeElement($employee)) {
            // set the owning side to null (unless already changed)
            if ($employee->getUser() === $this) {
                $employee->setUser(null);
            }
        }

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }
}
