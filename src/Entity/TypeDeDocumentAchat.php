<?php

namespace App\Entity;

use App\Repository\TypeDeDocumentAchatRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeDeDocumentAchatRepository::class)]
class TypeDeDocumentAchat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $libéllé_du_type_de_document = null;

    #[ORM\Column(length: 255)]
    private ?string $no = null;

    #[ORM\Column(length: 255)]
    private ?string $Libellé_du_type_de_document = null;

    #[ORM\ManyToOne(inversedBy: 'TypeDeDocumentAchat')]
    #[ORM\JoinColumn(nullable: false)]
    private ?DocumentAchat $documentAchat = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibélléDuTypeDeDocument(): ?string
    {
        return $this->libéllé_du_type_de_document;
    }

    public function setLibélléDuTypeDeDocument(string $libéllé_du_type_de_document): static
    {
        $this->libéllé_du_type_de_document = $libéllé_du_type_de_document;

        return $this;
    }

    public function getNo(): ?string
    {
        return $this->no;
    }

    public function setNo(string $no): static
    {
        $this->no = $no;

        return $this;
    }

    public function getLibelléDuTypeDeDocument(): ?string
    {
        return $this->Libellé_du_type_de_document;
    }

    public function setLibelléDuTypeDeDocument(string $Libellé_du_type_de_document): static
    {
        $this->Libellé_du_type_de_document = $Libellé_du_type_de_document;

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
