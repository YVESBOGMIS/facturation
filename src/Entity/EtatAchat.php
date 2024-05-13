<?php

namespace App\Entity;

use App\Repository\EtatAchatRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtatAchatRepository::class)]
class EtatAchat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $Libellé_état_Achat = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelléétatAchat(): ?string
    {
        return $this->Libellé_état_Achat;
    }

    public function setLibelléétatAchat(string $Libellé_état_Achat): static
    {
        $this->Libellé_état_Achat = $Libellé_état_Achat;

        return $this;
    }
}
