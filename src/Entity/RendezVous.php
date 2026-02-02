<?php

namespace App\Entity;

use App\Repository\RendezVousRepository;
use BcMath\Number;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RendezVousRepository::class)]
class RendezVous
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTime $datetime = null;

    #[ORM\Column(length: 50)]
    private ?string $statut = 'demande';

    #[ORM\Column(length: 50)]
    private ?string $mode = null;

    #[ORM\Column(length: 255)]
    private ?string $motif = null;

    #[ORM\Column]
    private ?\DateTime $createdAt = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $idPatient = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $idStaff = null;
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatetime(): ?\DateTime
    {
        return $this->datetime;
    }

    public function setDatetime(\DateTime $datetime): static
    {
        $this->datetime = $datetime;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getMode(): ?string
    {
        return $this->mode;
    }

    public function setMode(string $mode): static
    {
        $this->mode = $mode;

        return $this;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(string $motif): static
    {
        $this->motif = $motif;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getIdPatient(): ?int
    {
        return $this->idPatient;
    }

    public function setIdPatient(int $idPatient): static
    {
        $this->idPatient = $idPatient;

        return $this;
    }

    public function getIdStaff(): ?int
    {
        return $this->idStaff;
    }

    public function setIdStaff(int $idStaff): static
    {
        $this->idStaff = $idStaff;

        return $this;
    }
}
