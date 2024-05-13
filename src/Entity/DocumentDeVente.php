<?php

namespace App\Entity;

use App\Repository\DocumentDeVenteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DocumentDeVenteRepository::class)]
class DocumentDeVente
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'documentDeVentes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Vente $vente = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $DocumentDeVente = null;

    #[ORM\ManyToOne(inversedBy: 'documentDeVente')]
    private ?TypeDeDocumentDeVente $Type_Document_Vente = null;

    #[ORM\OneToMany(targetEntity: LigneDeVente::class, mappedBy: 'DocumentDeVente')]
    private Collection $ligneDeVentes;

    public function __construct()
    {
        $this->ligneDeVentes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVente(): ?Vente
    {
        return $this->vente;
    }

    public function setVente(?Vente $vente): static
    {
        $this->vente = $vente;

        return $this;
    }

    public function getDocumentDeVente(): ?string
    {
        return $this->DocumentDeVente;
    }

    public function setDocumentDeVente(?string $DocumentDeVente): static
    {
        $this->DocumentDeVente = $DocumentDeVente;

        return $this;
    }

    public function getTypeDocumentVente(): ?TypeDeDocumentDeVente
    {
        return $this->Type_Document_Vente;
    }

    public function setTypeDocumentVente(?TypeDeDocumentDeVente $Type_Document_Vente): static
    {
        $this->Type_Document_Vente = $Type_Document_Vente;

        return $this;
    }

    public function addLigneDeVente(LigneDeVente $ligne_de_vente)
    {
    }

    public function setTypeDocument(float|bool|int|string|null $typeDocument)
    {

    }

    public function setLigneDeVente(LigneDeVente $ligneDeVente)
    {
    }

    /**
     * @return Collection<int, LigneDeVente>
     */
    public function getLigneDeVentes(): Collection
    {
        return $this->ligneDeVentes;
    }

    public function removeLigneDeVente(LigneDeVente $ligneDeVente): static
    {
        if ($this->ligneDeVentes->removeElement($ligneDeVente)) {
            // set the owning side to null (unless already changed)
            if ($ligneDeVente->getDocumentDeVente() === $this) {
                $ligneDeVente->setDocumentDeVente(null);
            }
        }

        return $this;
    }



}
