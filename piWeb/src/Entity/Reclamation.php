<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_reclamation")]
    private ?int $id_reclamation = null;

    #[ORM\Column(length: 30)]
    private ?string $referenceReclamation = null;

    #[ORM\Column(length: 150)]
    private ?string $contenu = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 50)]
    private ?string $type = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pieceJointePath = null;

    #[ORM\Column(length: 50)]
    private ?string $statutReclamation = null;

    #[ORM\Column(length: 50)]
    private ?string $priorite = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $date_limite = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date_creation_r = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $date_modification_r = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $date_cloture_r = null;

    /**
     * @var Collection<int, ReponseReclamation>
     */
    #[ORM\OneToMany(targetEntity: ReponseReclamation::class, mappedBy: 'reclamation', orphanRemoval: true)]
    private Collection $reponses;

    public function __construct()
    {
        $this->reponses = new ArrayCollection();
    }


    public function getIdReclamation(): ?int
    {
        return $this->id_reclamation;
    }


    public function getReferenceReclamation(): ?string
    {
        return $this->referenceReclamation;
    }

    public function setReferenceReclamation(string $referenceReclamation): static
    {
        $this->referenceReclamation = $referenceReclamation;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): static
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getPieceJointePath(): ?string
    {
        return $this->pieceJointePath;
    }

    public function setPieceJointePath(?string $pieceJointePath): static
    {
        $this->pieceJointePath = $pieceJointePath;

        return $this;
    }

    public function getStatutReclamation(): ?string
    {
        return $this->statutReclamation;
    }

    public function setStatutReclamation(string $statutReclamation): static
    {
        $this->statutReclamation = $statutReclamation;

        return $this;
    }

    public function getPriorite(): ?string
    {
        return $this->priorite;
    }

    public function setPriorite(string $priorite): static
    {
        $this->priorite = $priorite;

        return $this;
    }

    public function getDateLimite(): ?\DateTimeImmutable
    {
        return $this->date_limite;
    }

    public function setDateLimite(?\DateTimeImmutable $date_limite): static
    {
        $this->date_limite = $date_limite;

        return $this;
    }

    public function getDateCreationR(): ?\DateTimeImmutable
    {
        return $this->date_creation_r;
    }

    public function setDateCreationR(\DateTimeImmutable $date_creation_r): static
    {
        $this->date_creation_r = $date_creation_r;

        return $this;
    }

    public function getDateModificationR(): ?\DateTimeImmutable
    {
        return $this->date_modification_r;
    }

    public function setDateModificationR(?\DateTimeImmutable $date_modification_r): static
    {
        $this->date_modification_r = $date_modification_r;

        return $this;
    }

    public function getDateClotureR(): ?\DateTimeImmutable
    {
        return $this->date_cloture_r;
    }

    public function setDateClotureR(?\DateTimeImmutable $date_cloture_r): static
    {
        $this->date_cloture_r = $date_cloture_r;

        return $this;
    }

    /**
     * @return Collection<int, ReponseReclamation>
     */
    public function getReponses(): Collection
    {
        return $this->reponses;
    }

    public function addReponse(ReponseReclamation $reponse): static
    {
        if (!$this->reponses->contains($reponse)) {
            $this->reponses->add($reponse);
            $reponse->setReclamation($this);
        }

        return $this;
    }

    public function removeReponse(ReponseReclamation $reponse): static
    {
        if ($this->reponses->removeElement($reponse)) {
            // set the owning side to null (unless already changed)
            if ($reponse->getReclamation() === $this) {
                $reponse->setReclamation(null);
            }
        }

        return $this;
    }
}
