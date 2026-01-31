<?php

namespace App\Controller;

use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

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

    #[Route('/augmenter/{id}', name: 'panier_augmenter')]
    public function augmenter(int $id, SessionInterface $session, EntityManagerInterface $em): JsonResponse
    {
        $panier = $session->get('panier', []);
        
        if (isset($panier[$id])) {
            $panier[$id]['quantite']++;
            $session->set('panier', $panier);
            
            // Calculer le nouveau total
            $total = 0;
            $totalItems = 0;
            foreach ($panier as $item) {
                $total += $item['prix'] * $item['quantite'];
                $totalItems += $item['quantite'];
            }
            
            $produit = $em->getRepository(Produit::class)->find($id);
            
            return new JsonResponse([
                'success' => true,
                'quantite' => $panier[$id]['quantite'],
                'total' => $total,
                'totalItems' => $totalItems,
                'message' => 'Quantité augmentée'
            ]);
        }
        
        return new JsonResponse(['success' => false, 'message' => 'Produit introuvable']);
    }

    #[Route('/diminuer/{id}', name: 'panier_diminuer')]
    public function diminuer(int $id, SessionInterface $session, EntityManagerInterface $em): JsonResponse
    {
        $panier = $session->get('panier', []);
        
        if (isset($panier[$id])) {
            $panier[$id]['quantite']--;
            
            if ($panier[$id]['quantite'] <= 0) {
                unset($panier[$id]);
                $message = 'Produit retiré du panier';
            } else {
                $message = 'Quantité diminuée';
            }
            
            $session->set('panier', $panier);
            
            // Calculer le nouveau total
            $total = 0;
            $totalItems = 0;
            foreach ($panier as $item) {
                $total += $item['prix'] * $item['quantite'];
                $totalItems += $item['quantite'];
            }
            
            return new JsonResponse([
                'success' => true,
                'quantite' => isset($panier[$id]) ? $panier[$id]['quantite'] : 0,
                'total' => $total,
                'totalItems' => $totalItems,
                'message' => $message
            ]);
        }
        
        return new JsonResponse(['success' => false, 'message' => 'Produit introuvable']);
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