<?php

namespace App\Entity;

use App\Repository\ReponseReclamationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReponseReclamationRepository::class)]
class ReponseReclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_reponse")]
    private ?int $id_reponse = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $message = null;

    #[ORM\Column(length: 50)]
    private ?string $typeReponse = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date_creation_rep = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date_modification_rep = null;

    #[ORM\ManyToOne(inversedBy: 'reponses')]
    #[ORM\JoinColumn(name: "id_reclamation", referencedColumnName: "id_reclamation", nullable: false, onDelete: "CASCADE")]
    private ?Reclamation $reclamation = null;


    public function getIdReponse(): ?int
    {
        return $this->id_reponse;
    }

    public function setIdReponse(int $id_reponse): static
    {
        $this->id_reponse = $id_reponse;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getTypeReponse(): ?string
    {
        return $this->typeReponse;
    }

    public function setTypeReponse(string $typeReponse): static
    {
        $this->typeReponse = $typeReponse;

        return $this;
    }

    public function getDateCreationRep(): ?\DateTimeImmutable
    {
        return $this->date_creation_rep;
    }

    public function setDateCreationRep(\DateTimeImmutable $date_creation_rep): static
    {
        $this->date_creation_rep = $date_creation_rep;

        return $this;
    }

    public function getDateModificationRep(): ?\DateTimeImmutable
    {
        return $this->date_modification_rep;
    }

    public function setDateModificationRep(\DateTimeImmutable $date_modification_rep): static
    {
        $this->date_modification_rep = $date_modification_rep;

        return $this;
    }

    public function getReclamation(): ?Reclamation
    {
        return $this->reclamation;
    }

    public function setReclamation(?Reclamation $reclamation): static
    {
        $this->reclamation = $reclamation;

        return $this;
    }
}
