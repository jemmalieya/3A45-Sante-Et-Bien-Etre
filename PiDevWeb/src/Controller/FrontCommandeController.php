<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/commande')]
class FrontCommandeController extends AbstractController
{
    // ✅ ÉTAPE 1 : Afficher la page de validation (récapitulatif)
    #[Route('/valider', name: 'commande_valider')]
    public function valider(SessionInterface $session, EntityManagerInterface $em): Response
    {
        $panier = $session->get('panier', []);

        // Vérifier que le panier n'est pas vide
        if (empty($panier)) {
            $this->addFlash('error', 'Votre panier est vide');
            return $this->redirectToRoute('front_produit_index');
        }

        // Préparer les données pour l'affichage
        $produitsPanier = [];
        $total = 0;

        foreach ($panier as $id => $item) {
            $produit = $em->getRepository(Produit::class)->find($id);
            
            if (!$produit) {
                continue;
            }

            // ✅ Vérifier la disponibilité
            if ($produit->getStatusProduit() !== 'Disponible') {
                $this->addFlash('error', $produit->getNomProduit() . ' n\'est plus disponible');
                return $this->redirectToRoute('front_produit_index');
            }

            // ✅ Vérifier le stock
            if ($produit->getQuantiteProduit() < $item['quantite']) {
                $this->addFlash('error', 'Stock insuffisant pour ' . $produit->getNomProduit());
                return $this->redirectToRoute('front_produit_index');
            }

            $produit->quantite_panier = $item['quantite'];
            $produitsPanier[] = $produit;
            $total += $produit->getPrixProduit() * $item['quantite'];
        }

        // ✅ Afficher la page de confirmation avec récapitulatif
        return $this->render('front_commande/valider.html.twig', [
            'produits' => $produitsPanier,
            'total' => $total
        ]);
    }

    // ✅ ÉTAPE 2 : Confirmer la commande (après que l'utilisateur clique "Oui")
    #[Route('/confirmer', name: 'commande_confirmer', methods: ['POST'])]
    public function confirmer(SessionInterface $session, EntityManagerInterface $em): Response
    {
        $panier = $session->get('panier', []);

        if (empty($panier)) {
            $this->addFlash('error', 'Votre panier est vide');
            return $this->redirectToRoute('front_produit_index');
        }

        // Créer la commande
        $commande = new Commande();
        $commande->setDateCreationCommande(new \DateTimeImmutable());
        $commande->setStatutCommande('En attente');
        $commande->setIdUser(1); // TODO: Remplacer par l'utilisateur connecté plus tard

        $total = 0;

        foreach ($panier as $id => $item) {
            $produit = $em->getRepository(Produit::class)->find($id);
            
            if (!$produit) {
                continue;
            }

            // ✅ Vérifier à nouveau (sécurité)
            if ($produit->getStatusProduit() !== 'Disponible' || 
                $produit->getQuantiteProduit() < $item['quantite']) {
                $this->addFlash('error', 'Problème avec ' . $produit->getNomProduit());
                return $this->redirectToRoute('front_produit_index');
            }

            // Créer la ligne de commande
            $ligne = new LigneCommande();
            $ligne->setProduit($produit);
            $ligne->setQuantite_commandee($item['quantite']);
            $ligne->setCommande($commande);

            $em->persist($ligne);
            $commande->addLigneCommande($ligne);

            // Calculer le total
            $total += $produit->getPrixProduit() * $item['quantite'];

            // ✅ IMPORTANT : Déduire du stock
            $nouveauStock = $produit->getQuantiteProduit() - $item['quantite'];
            $produit->setQuantiteProduit($nouveauStock);

            // ✅ Mettre à jour le statut si stock = 0
            if ($nouveauStock <= 0) {
                $produit->setStatusProduit('Rupture');
            }
        }

        $commande->setMontantTotal($total);

        $em->persist($commande);
        $em->flush();

        // Vider le panier
        $session->remove('panier');

        $this->addFlash('success', 'Commande validée avec succès !');

        return $this->redirectToRoute('commande_details', ['id' => $commande->getIdCommande()]);
    }

    // ✅ ÉTAPE 3 : Afficher les détails de la commande
    #[Route('/details/{id}', name: 'commande_details')]
    public function details(Commande $commande): Response
    {
        // TODO: Vérifier que c'est bien la commande de l'utilisateur connecté
        // if ($commande->getIdUser() !== $this->getUser()->getId()) {
        //     throw $this->createAccessDeniedException();
        // }

        return $this->render('front_commande/details.html.twig', [
            'commande' => $commande,
        ]);
    }

    // ✅ BONUS : Liste de toutes les commandes de l'utilisateur
    #[Route('/mes-commandes', name: 'mes_commandes')]
    public function mesCommandes(EntityManagerInterface $em): Response
    {
        // TODO: Filtrer par utilisateur connecté
        // $commandes = $em->getRepository(Commande::class)->findBy(
        //     ['id_user' => $this->getUser()->getId()],
        //     ['date_creation_commande' => 'DESC']
        // );

        // Pour l'instant, afficher toutes les commandes
        $commandes = $em->getRepository(Commande::class)->findAll();

        return $this->render('front_commande/mes_commandes.html.twig', [
            'commandes' => $commandes
        ]);
    }
}