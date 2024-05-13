<?php

namespace App\Entity;

use App\Repository\EncaissementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EncaissementRepository::class)]
class Encaissement

{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'encaissements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Vente $vente = null;

    #[ORM\Column(type: "integer")]
    private ?string $Montant = null;

    #[ORM\Column(length: 255)]
    private ?string $Mode_de_paiement = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $Date = null;

    public function getVente(): ?Vente
    {
        return $this->vente;
    }

    public function setVente(?Vente $vente): static
    {
        $this->vente = $vente;

        return $this;
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



    public function getModeDePaiement(): ?string
    {
        return $this->Mode_de_paiement;
    }

    public function setModeDePaiement(string $Mode_de_paiement): static
    {
        $this->Mode_de_paiement = $Mode_de_paiement;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->Date;
    }

    public function setDate(\DateTimeInterface $Date): static
    {
        $this->Date = $Date;

        return $this;
    }


    public function annuler(): void
    {


        // Vérifier si l'encaissement n'est pas déjà annulée
        if ($this->annulee) {
            // Si elle est déjà annulée, ne rien faire
            return;
        }

        // Marquer l'encaissement comme annulée
        $this->annulee = true;

        // Enregistrer les modifications dans la base de données
        $this->entityManager->flush();

    }

    public function getId()
    {
        return $this->id;
    }


}