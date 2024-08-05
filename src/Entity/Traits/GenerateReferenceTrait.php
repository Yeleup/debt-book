<?php

namespace App\Entity\Traits;

use App\Entity\Expense;
use App\Entity\Transfer;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait GenerateReferenceTrait
{
    #[ORM\Column(length: 255)]
    #[Groups(['employee:transfers', 'employee:banks', 'transfer.read', 'expense.read'])]
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
        } elseif ($this instanceof Expense) {
            $this->reference = 'EXPENSE-' . strtoupper(uniqid());
        } else {
            $this->reference = strtoupper(uniqid());
        }
    }
}