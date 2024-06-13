<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use App\Repository\UserOrganizationRepository;
use App\State\UserOrganizationStateProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use App\Validator\UserOrganization\UniqueUserOrganization;

#[ApiResource(
    shortName: 'Apply',
    operations: [
        new Post(),
    ],
    normalizationContext: ['groups' => ['user_organization.read']],
    denormalizationContext: ['groups' => ['user_organization.write']],
    processor: UserOrganizationStateProcessor::class
)]
#[ApiResource(
    uriTemplate: '/organizations/{organizationId}/applies',
    shortName: 'Apply',
    operations: [new GetCollection()],
    uriVariables: [
        'organizationId' => new Link(toProperty: 'organization', fromClass: Organization::class),
    ],
    paginationItemsPerPage: 10
)]
#[ORM\Entity(repositoryClass: UserOrganizationRepository::class)]
#[ORM\UniqueConstraint(name: 'user_organization_unique', fields: ['user', 'organization'])]
#[UniqueUserOrganization]
class UserOrganization
{
    const ROLE_OWNER = 'owner';
    const ROLE_ADMIN = 'admin';
    const ROLE_MEMBER = 'member';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user_organization.read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userOrganizations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['user_organization.read'])]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'userOrganizations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['user_organization.read', 'user_organization.write', 'user.me'])]
    private ?Organization $organization = null;

    #[ORM\Column(length: 50)]
    #[Groups(['user_organization.read'])]
    private ?string $role = null;

    #[ORM\Column]
    #[Groups(['user_organization.read'])]
    private ?bool $status = null;

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
}
