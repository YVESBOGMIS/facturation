<?php

namespace App\Entity;

use App\Repository\PaiementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaiementRepository::class)]
class Paiement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $Montant = null;

    #[ORM\Column(length: 255)]
    private ?string $Date_de_paiement = null;

    #[ORM\Column(length: 255)]
    private ?string $Mode_de_paiement = null;

    #[ORM\ManyToOne(inversedBy: 'paiement')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Achat $paiementAchat = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?int
    {
        return $this->Montant;
    }

    public function setMontant(int $Montant): static
    {
        $this->Montant = $Montant;

        return $this;
    }

    public function getDateDePaiement(): ?string
    {
        return $this->Date_de_paiement;
    }

    public function setDateDePaiement(string $Date_de_paiement): static
    {
        $this->Date_de_paiement = $Date_de_paiement;

        return $this;
    }

    public function getModeDePaiement(): ?string
    {
        return $this->Mode_de_paiement;
    }

    public function setModeDePaiement(string $Mode_de_paiement): static
    {
        $this->Mode_de_paiement = $Mode_de_paiement;

        return $this;
    }

    public function getPaiementAchat(): ?Achat
    {
        return $this->paiementAchat;
    }

    public function setPaiementAchat(?Achat $paiementAchat): static
    {
        $this->paiementAchat = $paiementAchat;

        return $this;
    }
}
