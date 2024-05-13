<?php

namespace App\Entity;

use App\Repository\LigneAchatRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LigneAchatRepository::class)]
class LigneAchat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $Quantite = null;

    #[ORM\Column(length: 255)]
    private ?string $Prix_unitaire = null;

    #[ORM\ManyToOne(inversedBy: 'LigneAchat')]
    #[ORM\JoinColumn(nullable: false)]
    private ?DocumentAchat $documentAchat = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantite(): ?int
    {
        return $this->Quantite;
    }

    public function setQuantite(int $Quantite): static
    {
        $this->Quantite = $Quantite;

        return $this;
    }

    public function getPrixUnitaire(): ?string
    {
        return $this->Prix_unitaire;
    }

    public function setPrixUnitaire(string $Prix_unitaire): static
    {
        $this->Prix_unitaire = $Prix_unitaire;

        return $this;
    }

    public function getDocumentAchat(): ?DocumentAchat
    {
        return $this->documentAchat;
    }

    public function setDocumentAchat(?DocumentAchat $documentAchat): static
    {
        $this->documentAchat = $documentAchat;

        return $this;
    }
}
