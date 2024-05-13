<?php

namespace App\Entity;

use AllowDynamicProperties;
use App\Repository\VenteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[AllowDynamicProperties] #[ORM\Entity(repositoryClass: VenteRepository::class)]
class Vente
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;



    #[ORM\ManyToOne(inversedBy: 'vente')]

    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'ventes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?EtatDeVente $etat_de_vente = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_de_vente = null;

    #[ORM\OneToMany(targetEntity: DocumentDeVente::class, mappedBy: 'vente', orphanRemoval: true)]
    private Collection $documentDeVentes;

    #[ORM\OneToMany(targetEntity: Encaissement::class, mappedBy: 'vente', orphanRemoval: true)]
    private Collection $encaissements;

    #[ORM\Column(length: 255)]
    private ?string $Montant_Total = null;

    #[ORM\ManyToOne(inversedBy: 'ventes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Clients $client = null;




    public function __construct()
    {
        $this->encaissementvente = new ArrayCollection();
        $this->documentDeVentes = new ArrayCollection();
        $this->encaissements = new ArrayCollection();
    }


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



    public function getEtatDeVente(): ?EtatDeVente
    {
        return $this->etat_de_vente;
    }

    public function setEtatDeVente(?EtatDeVente $etat_de_vente): static
    {
        $this->etat_de_vente = $etat_de_vente;

        return $this;
    }

    public function getDateDeVente(): ?\DateTimeInterface
    {
        return $this->date_de_vente;
    }

    public function setDateDeVente(\DateTimeInterface $date_de_vente): static
    {
        $this->date_de_vente = $date_de_vente;

        return $this;
    }

    /**
     * @return Collection<int, DocumentDeVente>
     */
    public function getDocumentDeVentes(): Collection
    {
        return $this->documentDeVentes;
    }

    public function addDocumentDeVente(DocumentDeVente $documentDeVente): static
    {
        if (!$this->documentDeVentes->contains($documentDeVente)) {
            $this->documentDeVentes->add($documentDeVente);
            $documentDeVente->setVente($this);
        }

        return $this;
    }

    public function removeDocumentDeVente(DocumentDeVente $documentDeVente): static
    {
        if ($this->documentDeVentes->removeElement($documentDeVente)) {
            // set the owning side to null (unless already changed)
            if ($documentDeVente->getVente() === $this) {
                $documentDeVente->setVente(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Encaissement>
     */
    public function getEncaissements(): Collection
    {
        return $this->encaissements;
    }

    public function addEncaissement(Encaissement $encaissement): static
    {
        if (!$this->encaissements->contains($encaissement)) {
            $this->encaissements->add($encaissement);
            $encaissement->setVente($this);
        }

        return $this;
    }

    public function removeEncaissement(Encaissement $encaissement): static
    {
        if ($this->encaissements->removeElement($encaissement)) {
            // set the owning side to null (unless already changed)
            if ($encaissement->getVente() === $this) {
                $encaissement->setVente(null);
            }
        }

        return $this;
    }

    public function getMontantTotal(): ?string
    {
        return $this->Montant_Total;
    }

    public function setMontantTotal(string $Montant_Total): static
    {
        $this->Montant_Total = $Montant_Total;

        return $this;
    }

    public function getClient(): ?Clients
    {
        return $this->client;
    }

    public function setClient(?Clients $client): static
    {
        $this->client = $client;

        return $this;
    }





}
