<?php

namespace App\Controller;

use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


#[Route('/panier')]
class FrontPanierController extends AbstractController
{
    #[Route('', name: 'panier_index')]
    public function index(SessionInterface $session, EntityManagerInterface $em): Response
    {
        $panier = $session->get('panier', []);

        $produitsPanier = [];
        $total = 0;

        foreach ($panier as $id => $item) {
            $produit = $em->getRepository(Produit::class)->find($id);
            if ($produit) {
                $produit->quantite_panier = $item['quantite'];
                $produitsPanier[] = $produit;
                $total += $produit->getPrixProduit() * $item['quantite'];
            }
        }

        return $this->render('front_panier/panier_sidebar.html.twig', [
            'produits' => $produitsPanier,
            'total' => $total
        ]);
    }

    #[Route('/ajouter/{id}', name: 'panier_ajouter')]
    public function ajouter(Produit $produit, SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);
        $id = $produit->getId_produit();

        if (isset($panier[$id])) {
            $panier[$id]['quantite']++;
        } else {
            $panier[$id] = [
                'quantite' => 1,
                'prix' => $produit->getPrixProduit()
            ];
        }

        $session->set('panier', $panier);
        $this->addFlash('success', $produit->getNomProduit() . ' ajouté au panier avec succès');

        return $this->redirectToRoute('front_produit_index');
    }

    #[Route('/augmenter/{id}', name: 'panier_augmenter', methods: ['POST'])]
    public function augmenter(Produit $produit, SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);
        $id = $produit->getId_produit();
    
        if (!isset($panier[$id])) {
            $panier[$id] = [
                'quantite' => 0,
                'prix' => $produit->getPrixProduit()
            ];
        }
    
        $stock = (int) ($produit->getQuantiteProduit() ?? 0);
    
        if ($stock > 0 && $panier[$id]['quantite'] >= $stock) {
            return new Response('Produit épuisé', 400);
        }
    
        $panier[$id]['quantite']++;
        $session->set('panier', $panier);
    
        return new Response('Quantité augmentée');
    }
    
    #[Route('/diminuer/{id}', name: 'panier_diminuer', methods: ['POST'])]
    public function diminuer(Produit $produit, SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);
        $id = $produit->getId_produit();
    
        if (!isset($panier[$id])) {
            return new Response('Produit introuvable', 400);
        }
    
        $panier[$id]['quantite']--;
    
        if ($panier[$id]['quantite'] <= 0) {
            unset($panier[$id]);
            $session->set('panier', $panier);
            return new Response('Produit retiré du panier');
        }
    
        $session->set('panier', $panier);
        return new Response('Quantité diminuée');
    }
    
    #[Route('/supprimer/{id}', name: 'panier_supprimer')]
    public function supprimer(Produit $produit, SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);
        $id = $produit->getId_produit();

        if (isset($panier[$id])) {
            unset($panier[$id]);
            $session->set('panier', $panier);
            $this->addFlash('success', $produit->getNomProduit() . ' supprimé du panier');
        }

        return $this->redirectToRoute('panier_index');
    }

    #[Route('/vider', name: 'panier_vider')]
    public function vider(SessionInterface $session): Response
    {
        $session->remove('panier');
        $this->addFlash('success', 'Panier vidé avec succès');
        return $this->redirectToRoute('front_produit_index');
    }
}