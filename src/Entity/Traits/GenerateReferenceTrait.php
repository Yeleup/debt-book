<?php

namespace App\Entity\Traits;

use App\Entity\Transfer;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait GenerateReferenceTrait
{
    #[ORM\Column(length: 255)]
    #[Groups(['transfer.read'])]
    private ?string $reference = null;

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    #[ORM\PrePersist]
    public function setReferenceValue(): void
    {
        if ($this instanceof Transfer) {
            $this->reference = 'TRANSFER-' . strtoupper(uniqid());
        } else {
            $this->reference = strtoupper(uniqid());
        }
    }
}