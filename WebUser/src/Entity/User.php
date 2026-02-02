<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity(fields: ['emailUser'], message: 'Cet email est déjà utilisé.')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * Mot de passe non hashé (utilisé seulement dans les formulaires)
     */
    #[Assert\Length(
        min: 8,
        minMessage: "Le mot de passe doit contenir au moins {{ limit }} caractères."
    )]
    #[Assert\Regex(
        pattern: "/^(?=.*[A-Za-z])(?=.*\d).+$/",
        message: "Le mot de passe doit contenir au moins une lettre et un chiffre."
    )]
    private ?string $plainPassword = null;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 8)]
    #[Assert\NotBlank(message: "Le CIN est obligatoire.")]
    #[Assert\Length(
        min: 8,
        max: 8,
        exactMessage: "Le CIN doit contenir exactement {{ limit }} chiffres."
    )]
    #[Assert\Regex(
        pattern: "/^\d{8}$/",
        message: "Le CIN doit contenir uniquement 8 chiffres."
    )]
    private ?string $cin = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: "Le nom du fichier image est trop long."
    )]
    private ?string $profilePicture = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "Le nom est obligatoire.")]
    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: "Le nom doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le nom ne doit pas dépasser {{ limit }} caractères."
    )]
    #[Assert\Regex(
        pattern: "/^[A-Za-zÀ-ÿ\s'-]+$/u",
        message: "Le nom ne doit contenir que des lettres."
    )]
    private ?string $nom = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "Le prénom est obligatoire.")]
    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: "Le prénom doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le prénom ne doit pas dépasser {{ limit }} caractères."
    )]
    #[Assert\Regex(
        pattern: "/^[A-Za-zÀ-ÿ\s'-]+$/u",
        message: "Le prénom ne doit contenir que des lettres."
    )]
    private ?string $prenom = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: "La date de naissance est obligatoire.")]
    #[Assert\LessThanOrEqual("today", message: "La date de naissance ne peut pas être dans le futur.")]
    #[Assert\GreaterThan("1900-01-01", message: "Veuillez saisir une date de naissance valide.")]
    private ?\DateTimeInterface $dateNaissance = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: "Le numéro de téléphone est obligatoire.")]
    #[Assert\Length(
        min: 8,
        max: 20,
        minMessage: "Le téléphone doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le téléphone ne doit pas dépasser {{ limit }} caractères."
    )]
    #[Assert\Regex(
        pattern: "/^\+?\d{8,20}$/",
        message: "Téléphone invalide. Exemple: 12345678 ou +21612345678."
    )]
    private ?string $telephoneUser = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: "L'email est obligatoire.")]
    #[Assert\Email(message: "Veuillez saisir un email valide.")]
    #[Assert\Length(
        max: 180,
        maxMessage: "L'email ne doit pas dépasser {{ limit }} caractères."
    )]
    private ?string $emailUser = null;

    #[ORM\Column(length: 180, nullable: true)]
    #[Assert\Length(
        max: 180,
        maxMessage: "L'adresse ne doit pas dépasser {{ limit }} caractères."
    )]
    private ?string $adresseUser = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le mot de passe est obligatoire.")]
    #[Assert\Length(
        min: 8,
        max: 255,
        minMessage: "Le mot de passe doit contenir au moins {{ limit }} caractères."
    )]
    private ?string $password = null;

    #[ORM\Column(length: 30, nullable: true)]
    #[Assert\Choice(
        choices: ["ACTIVE", "RESTRICTED", "BANNED"],
        message: "Statut invalide (ACTIVE, RESTRICTED ou BANNED)."
    )]
    private ?string $statutCompte = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeInterface $derniereConnexion = null;

    #[ORM\Column]
    private bool $isVerified = false;

    // Getter and setter for profile picture
    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(?string $profilePicture): self
    {
        $this->profilePicture = $profilePicture;
        return $this;
    }

    // Getter and setter for plainPassword (for password form submission)
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    // Getter and setter for password (hashed password)
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    // Getter and setter for other fields
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCin(): ?string
    {
        return $this->cin;
    }

    public function setCin(string $cin): self
    {
        $this->cin = $cin;
        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(\DateTimeInterface $dateNaissance): self
    {
        $this->dateNaissance = $dateNaissance;
        return $this;
    }

    public function getTelephoneUser(): ?string
    {
        return $this->telephoneUser;
    }

    public function setTelephoneUser(string $telephoneUser): self
    {
        $this->telephoneUser = $telephoneUser;
        return $this;
    }

    public function getEmailUser(): ?string
    {
        return $this->emailUser;
    }

    public function setEmailUser(string $emailUser): self
    {
        $this->emailUser = $emailUser;
        return $this;
    }

    public function getAdresseUser(): ?string
    {
        return $this->adresseUser;
    }

    public function setAdresseUser(?string $adresseUser): self
    {
        $this->adresseUser = $adresseUser;
        return $this;
    }

    public function getStatutCompte(): ?string
    {
        return $this->statutCompte;
    }

    public function setStatutCompte(?string $statutCompte): self
    {
        $this->statutCompte = $statutCompte;
        return $this;
    }

    public function getDerniereConnexion(): ?\DateTimeInterface
    {
        return $this->derniereConnexion;
    }

    public function setDerniereConnexion(?\DateTimeInterface $derniereConnexion): self
    {
        $this->derniereConnexion = $derniereConnexion;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->emailUser;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
        // Clear sensitive data if needed
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;
        return $this;
    }
}
