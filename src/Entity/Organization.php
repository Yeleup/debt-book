<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\OrganizationRepository;
use App\State\OrganizationStateProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: OrganizationRepository::class)]
#[ApiResource(
    operations: [
        new Post(),
        new GetCollection()
    ],
    normalizationContext: ['groups' => ['organization.read']],
    denormalizationContext: ['groups' => ['organization.write']],
    processor: OrganizationStateProcessor::class
)]
#[ORM\HasLifecycleCallbacks]
#[ApiFilter(SearchFilter::class, properties: ['code' => 'exact'])]
class Organization
{
    use Traits\TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['organization.read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['organization.read', 'organization.write'])]
    private ?string $title = null;

    #[ORM\Column(length: 6, nullable: true)]
    #[Groups(['organization.read', 'organization.write'])]
    private ?string $code = null;

    /**
     * @var Collection<int, UserOrganization>
     */
    #[ORM\OneToMany(targetEntity: UserOrganization::class, mappedBy: 'organization', cascade: ['persist'], orphanRemoval: true)]
    private Collection $userOrganizations;

    public function __construct()
    {
        $this->userOrganizations = new ArrayCollection();
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
     * @return Collection<int, UserOrganization>
     */
    public function getUserOrganizations(): Collection
    {
        return $this->userOrganizations;
    }

    public function addUserOrganization(UserOrganization $userOrganization): static
    {
        if (!$this->userOrganizations->contains($userOrganization)) {
            $this->userOrganizations->add($userOrganization);
            $userOrganization->setOrganization($this);
        }

        return $this;
    }

    public function removeUserOrganization(UserOrganization $userOrganization): static
    {
        if ($this->userOrganizations->removeElement($userOrganization)) {
            // set the owning side to null (unless already changed)
            if ($userOrganization->getOrganization() === $this) {
                $userOrganization->setOrganization(null);
            }
        }

        return $this;
    }
}
