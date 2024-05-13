<?php

namespace App\Entity;

use App\Repository\FournisseurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FournisseurRepository::class)]
class Fournisseur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Nom = null;

    #[ORM\Column(length: 255)]
    private ?string $Email = null;

    #[ORM\Column]
    private ?int $Téléphone = null;

    #[ORM\ManyToOne(inversedBy: 'Fournisseur')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $users = null;

    #[ORM\OneToMany(targetEntity: Achat::class, mappedBy: 'Fournisseur')]
    private Collection $fournisseurAchat;

    public function __construct()
    {
        $this->fournisseurAchat = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(string $Nom): static
    {
        $this->Nom = $Nom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->Email;
    }

    public function setEmai(string $Email): static
    {
        $this->Emai = $Email;

        return $this;
    }

    public function getTéléphone(): ?int
    {
        return $this->Téléphone;
    }

    public function setTéléphone(int $Téléphone): static
    {
        $this->Téléphone = $Téléphone;

        return $this;
    }

    public function getUsers(): ?Users
    {
        return $this->users;
    }

    public function setUsers(?Users $users): static
    {
        $this->users = $users;

        return $this;
    }

    /**
     * @return Collection<int, Achat>
     */
    public function getFournisseurAchat(): Collection
    {
        return $this->fournisseurAchat;
    }

    public function addFournisseurAchat(Achat $fournisseurAchat): static
    {
        if (!$this->fournisseurAchat->contains($fournisseurAchat)) {
            $this->fournisseurAchat->add($fournisseurAchat);
            $fournisseurAchat->setFournisseur($this);
        }

        return $this;
    }

    public function removeFournisseurAchat(Achat $fournisseurAchat): static
    {
        if ($this->fournisseurAchat->removeElement($fournisseurAchat)) {
            // set the owning side to null (unless already changed)
            if ($fournisseurAchat->getFournisseur() === $this) {
                $fournisseurAchat->setFournisseur(null);
            }
        }

        return $this;
    }
}
