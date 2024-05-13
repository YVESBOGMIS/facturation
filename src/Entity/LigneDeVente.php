<?php

namespace App\Entity;

use App\Repository\LigneDeVenteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LigneDeVenteRepository::class)]
class LigneDeVente
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Description = null;

    #[ORM\Column(type: "integer")]
    private ?string $Quantite = null;

    #[ORM\Column(type: "integer")]
    private ?string $Prix_unitaire = null;

    #[ORM\ManyToOne(inversedBy: 'ligneDeVentes',cascade: ["persist"])]
    #[ORM\JoinColumn(name: 'document_de_vente_id', referencedColumnName: 'id')]
    private ?DocumentDeVente $documentDeVente = null;

    #[ORM\Column(type: "integer")]
    private ?string $montant_total_l = null;

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): static
    {
        $this->Description = $Description;

        return $this;
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

    public function getPrixUnitaire(): ?int
    {
        return $this->Prix_unitaire;
    }

    public function setPrixUnitaire(int $Prix_unitaire): static
    {
        $this->Prix_unitaire = $Prix_unitaire;

        return $this;
    }

    public function getDocumentDeVente(): ?DocumentDeVente
    {
        return $this->documentDeVente;
    }

    public function setDocumentDeVente(?DocumentDeVente $documentDeVente): static
    {
        $this->documentDeVente = $documentDeVente;

        return $this;
    }

    public function getMontantTotalL(): ?int
    {
        return $this->montant_total_l;
    }

    public function setMontantTotalL(int $montant_total_l): static
    {
        $this->montant_total_l = $montant_total_l;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }


}
