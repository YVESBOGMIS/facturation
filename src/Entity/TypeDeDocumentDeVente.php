<?php

namespace App\Entity;

use App\Repository\TypeDeDocumentDeVenteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeDeDocumentDeVenteRepository::class)]
class TypeDeDocumentDeVente
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;



    #[ORM\OneToMany(targetEntity: DocumentDeVente::class, mappedBy: 'Type_Document_Vente')]
    private Collection $documentDeVentes;

    #[ORM\OneToMany(targetEntity: DocumentDeVente::class, mappedBy: 'Type_Document_Vente')]
    private Collection $documentDeVente;

    #[ORM\Column(length: 255)]
    private ?string $Nom = null;

    #[ORM\Column(length: 255)]
    private ?string $Slug = null;

    public function __construct()
    {
        $this->documentDeVentes = new ArrayCollection();
        $this->documentDeVente = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
            $documentDeVente->setTypeDocumentVente($this);
        }

        return $this;
    }

    public function removeDocumentDeVente(DocumentDeVente $documentDeVente): static
    {
        if ($this->documentDeVentes->removeElement($documentDeVente)) {
            // set the owning side to null (unless already changed)
            if ($documentDeVente->getTypeDocumentVente() === $this) {
                $documentDeVente->setTypeDocumentVente(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DocumentDeVente>
     */
    public function getDocumentDeVente(): Collection
    {
        return $this->documentDeVente;
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

    public function getSlug(): ?string
    {
        return $this->Slug;
    }

    public function setSlug(string $Slug): static
    {
        $this->Slug = $Slug;

        return $this;
    }
}
