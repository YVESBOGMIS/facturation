<?php

namespace App\Entity;

use App\Repository\DocumentAchatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DocumentAchatRepository::class)]
class DocumentAchat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $Type_de_document = null;

    #[ORM\Column(length: 255)]
    private ?string $Date_de_création = null;

    #[ORM\ManyToOne(inversedBy: 'DocumentAchat')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Achat $achat = null;

    #[ORM\OneToMany(targetEntity: TypeDeDocumentAchat::class, mappedBy: 'documentAchat')]
    private Collection $TypeDeDocumentAchat;

    #[ORM\OneToMany(targetEntity: LigneAchat::class, mappedBy: 'documentAchat')]
    private Collection $LigneAchat;

    public function __construct()
    {
        $this->TypeDeDocumentAchat = new ArrayCollection();
        $this->LigneAchat = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeDeDocument(): ?string
    {
        return $this->Type_de_document;
    }

    public function setTypeDeDocument(string $Type_de_document): static
    {
        $this->Type_de_document = $Type_de_document;

        return $this;
    }

    public function getDateDeCréation(): ?string
    {
        return $this->Date_de_création;
    }

    public function setDateDeCréation(string $Date_de_création): static
    {
        $this->Date_de_création = $Date_de_création;

        return $this;
    }

    public function getAchat(): ?Achat
    {
        return $this->achat;
    }

    public function setAchat(?Achat $achat): static
    {
        $this->achat = $achat;

        return $this;
    }

    /**
     * @return Collection<int, TypeDeDocumentAchat>
     */
    public function getTypeDeDocumentAchat(): Collection
    {
        return $this->TypeDeDocumentAchat;
    }

    public function addTypeDeDocumentAchat(TypeDeDocumentAchat $typeDeDocumentAchat): static
    {
        if (!$this->TypeDeDocumentAchat->contains($typeDeDocumentAchat)) {
            $this->TypeDeDocumentAchat->add($typeDeDocumentAchat);
            $typeDeDocumentAchat->setDocumentAchat($this);
        }

        return $this;
    }

    public function removeTypeDeDocumentAchat(TypeDeDocumentAchat $typeDeDocumentAchat): static
    {
        if ($this->TypeDeDocumentAchat->removeElement($typeDeDocumentAchat)) {
            // set the owning side to null (unless already changed)
            if ($typeDeDocumentAchat->getDocumentAchat() === $this) {
                $typeDeDocumentAchat->setDocumentAchat(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, LigneAchat>
     */
    public function getLigneAchat(): Collection
    {
        return $this->LigneAchat;
    }

    public function addLigneAchat(LigneAchat $ligneAchat): static
    {
        if (!$this->LigneAchat->contains($ligneAchat)) {
            $this->LigneAchat->add($ligneAchat);
            $ligneAchat->setDocumentAchat($this);
        }

        return $this;
    }

    public function removeLigneAchat(LigneAchat $ligneAchat): static
    {
        if ($this->LigneAchat->removeElement($ligneAchat)) {
            // set the owning side to null (unless already changed)
            if ($ligneAchat->getDocumentAchat() === $this) {
                $ligneAchat->setDocumentAchat(null);
            }
        }

        return $this;
    }
}
