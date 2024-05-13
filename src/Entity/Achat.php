<?php

namespace App\Entity;

use App\Repository\AchatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AchatRepository::class)]
class Achat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Date_Achat = null;

    #[ORM\Column(length: 255)]
    private ?string $Montantt_Total = null;

    #[ORM\ManyToOne(inversedBy: 'Achat')]
    private ?User $user = null;

    #[ORM\OneToMany(targetEntity: DocumentAchat::class, mappedBy: 'achat')]
    private Collection $DocumentAchat;

    #[ORM\OneToMany(targetEntity: Paiement::class, mappedBy: 'paiementAchat')]
    private Collection $paiement;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?EtatAchat $EtatAchat = null;

    #[ORM\ManyToOne(inversedBy: 'fournisseurAchat')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Fournisseur $Fournisseur = null;



    public function __construct()
    {
        $this->DocumentAchat = new ArrayCollection();
        $this->paiement = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateAchat(): ?string
    {
        return $this->Date_Achat;
    }

    public function setDateAchat(string $Date_Achat): static
    {
        $this->Date_Achat = $Date_Achat;

        return $this;
    }

    public function getMontanttTotal(): ?string
    {
        return $this->Montantt_Total;
    }

    public function setMontanttTotal(string $Montantt_Total): static
    {
        $this->Montantt_Total = $Montantt_Total;

        return $this;
    }

    public function getUsers(): ?User
    {
        return $this->user;
    }

    public function setUsers(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, DocumentAchat>
     */
    public function getDocumentAchat(): Collection
    {
        return $this->DocumentAchat;
    }

    public function addDocumentAchat(DocumentAchat $documentAchat): static
    {
        if (!$this->DocumentAchat->contains($documentAchat)) {
            $this->DocumentAchat->add($documentAchat);
            $documentAchat->setAchat($this);
        }

        return $this;
    }

    public function removeDocumentAchat(DocumentAchat $documentAchat): static
    {
        if ($this->DocumentAchat->removeElement($documentAchat)) {
            // set the owning side to null (unless already changed)
            if ($documentAchat->getAchat() === $this) {
                $documentAchat->setAchat(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Paiement>
     */
    public function getPaiement(): Collection
    {
        return $this->paiement;
    }

    public function addPaiement(Paiement $paiement): static
    {
        if (!$this->paiement->contains($paiement)) {
            $this->paiement->add($paiement);
            $paiement->setPaiementAchat($this);
        }

        return $this;
    }

    public function removePaiement(Paiement $paiement): static
    {
        if ($this->paiement->removeElement($paiement)) {
            // set the owning side to null (unless already changed)
            if ($paiement->getPaiementAchat() === $this) {
                $paiement->setPaiementAchat(null);
            }
        }

        return $this;
    }

    public function getEtatAchat(): ?EtatAchat
    {
        return $this->EtatAchat;
    }

    public function setEtatAchat(?EtatAchat $EtatAchat): static
    {
        $this->EtatAchat = $EtatAchat;

        return $this;
    }

    public function getFournisseur(): ?Fournisseur
    {
        return $this->Fournisseur;
    }

    public function setFournisseur(?Fournisseur $Fournisseur): static
    {
        $this->Fournisseur = $Fournisseur;

        return $this;
    }

    public function setEtat_achat_id(mixed $etat_achat_id)
    {
    }

    public function setUser_id(mixed $body)
    {
    }

    public function setFournisseur_id(mixed $fournisseur_id)
    {
    }

    public function setDate_achat(mixed $date_achat)
    {
    }

    public function setMontantt_total(mixed $montantt_total)
    {
    }


}
