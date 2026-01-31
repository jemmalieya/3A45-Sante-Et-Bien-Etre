<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id_produit = null;

    // Nom du produit
    #[ORM\Column(length: 150)]
    #[Assert\NotBlank(message: "Le nom du produit est obligatoire.")]
    private ?string $nom_produit = null;

    // Description du produit
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La description est obligatoire.")]
    private ?string $description_produit = null;

    // Prix du produit
    #[ORM\Column]
    #[Assert\Positive(message: "Le prix doit être positif.")]
    private ?float $prix_produit = null;

    // Quantité disponible
    #[ORM\Column]
    #[Assert\GreaterThanOrEqual(0, message: "La quantité ne peut pas être négative.")]
    private ?int $quantite_produit = null;

    // Image (URL)
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'image est obligatoire.")]
    #[Assert\Url(message: "L'image doit être une URL valide.")]
    private ?string $image_produit = null;

    // Catégorie du produit
    #[ORM\Column(length: 150)]
    #[Assert\NotBlank(message: "La catégorie est obligatoire.")]
    private ?string $categorie_produit = null;

    // Status du produit
    #[ORM\Column(length: 50)]
    #[Assert\Choice(choices: ['Disponible','Rupture','Expire'], message: "Status invalide.")]
    private ?string $status_produit = null;

    /**
     * @var Collection<int, LigneCommande>
     */
    #[ORM\OneToMany(targetEntity: LigneCommande::class, mappedBy: 'produit')]
    private Collection $ligne_commandes;

    public function __construct()
    {
        $this->ligne_commandes = new ArrayCollection();
    }

    // ID
    public function getId_produit(): ?int
    {
        return $this->id_produit;
    }

    // Nom
    public function getNomProduit(): ?string
    {
        return $this->nom_produit;
    }

    public function setNomProduit(string $nom_produit): static
    {
        $this->nom_produit = $nom_produit;
        return $this;
    }

    // Description
    public function getDescriptionProduit(): ?string
    {
        return $this->description_produit;
    }

    public function setDescriptionProduit(string $description_produit): static
    {
        $this->description_produit = $description_produit;
        return $this;
    }

    // Prix
    public function getPrixProduit(): ?float
    {
        return $this->prix_produit;
    }

    public function setPrixProduit(float $prix_produit): static
    {
        $this->prix_produit = $prix_produit;
        return $this;
    }

    // Quantité
    public function getQuantiteProduit(): ?int
    {
        return $this->quantite_produit;
    }

    public function setQuantiteProduit(int $quantite_produit): static
    {
        $this->quantite_produit = $quantite_produit;
        return $this;
    }

    // Image
    public function getImageProduit(): ?string
    {
        return $this->image_produit;
    }

    public function setImageProduit(string $image_produit): static
    {
        $this->image_produit = $image_produit;
        return $this;
    }

    // Catégorie
    public function getCategorieProduit(): ?string
    {
        return $this->categorie_produit;
    }

    public function setCategorieProduit(string $categorie_produit): static
    {
        $this->categorie_produit = $categorie_produit;
        return $this;
    }

    // Status
    public function getStatusProduit(): ?string
    {
        return $this->status_produit;
    }

    public function setStatusProduit(string $status_produit): static
    {
        $this->status_produit = $status_produit;
        return $this;
    }

    // Relation avec LigneCommande
    public function getLigneCommandes(): Collection
    {
        return $this->ligne_commandes;
    }

    public function addLigneCommande(LigneCommande $ligneCommande): static
    {
        if (!$this->ligne_commandes->contains($ligneCommande)) {
            $this->ligne_commandes->add($ligneCommande);
            $ligneCommande->setProduit($this);
        }
        return $this;
    }

    public function removeLigneCommande(LigneCommande $ligneCommande): static
    {
        if ($this->ligne_commandes->removeElement($ligneCommande)) {
            if ($ligneCommande->getProduit() === $this) {
                $ligneCommande->setProduit(null);
            }
        }
        return $this;
    }
}
