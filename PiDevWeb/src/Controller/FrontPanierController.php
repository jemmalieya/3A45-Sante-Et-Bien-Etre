<?php

namespace App\Controller;

use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

#[Route('/panier')]
class FrontPanierController extends AbstractController
{
    #[Route('', name: 'panier_index', methods: ['GET'])]
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

    // ✅ Compteur total d’articles dans le panier (pour badge navbar)
    #[Route('/count', name: 'panier_count', methods: ['GET'])]
    public function count(SessionInterface $session): JsonResponse
    {
        $panier = $session->get('panier', []);
        $count = 0;

        foreach ($panier as $item) {
            $count += (int)($item['quantite'] ?? 0);
        }

        return new JsonResponse(['count' => $count]);
    }

    // ✅ Retourne la quantité actuelle d’un produit dans le panier (pour vérifier stock)
    #[Route('/verifier/{id}', name: 'panier_verifier', methods: ['GET'])]
    public function verifier(Produit $produit, SessionInterface $session): JsonResponse
    {
        $panier = $session->get('panier', []);
        $id = $produit->getId_produit();

        $quantite = 0;
        if (isset($panier[$id])) {
            $quantite = (int) ($panier[$id]['quantite'] ?? 0);
        }

        return new JsonResponse(['quantite' => $quantite]);
    }

    // ✅ Ajouter au panier (AJAX-friendly) => renvoie JSON
    #[Route('/ajouter/{id}', name: 'panier_ajouter', methods: ['POST', 'GET'])]
    public function ajouter(Produit $produit, SessionInterface $session, Request $request): JsonResponse
    {
        $panier = $session->get('panier', []);
        $id = $produit->getId_produit();

        // Stock
        $stock = (int) ($produit->getQuantiteProduit() ?? 0);
        $quantiteDansPanier = isset($panier[$id]) ? (int)($panier[$id]['quantite'] ?? 0) : 0;

        if ($stock > 0 && $quantiteDansPanier >= $stock) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Stock insuffisant ! Seulement ' . $stock . ' disponible(s).',
                'count' => $this->getCount($panier)
            ], 400);
        }

        if (isset($panier[$id])) {
            $panier[$id]['quantite']++;
        } else {
            $panier[$id] = [
                'quantite' => 1,
                'prix' => $produit->getPrixProduit()
            ];
        }

        $session->set('panier', $panier);

        return new JsonResponse([
            'success' => true,
            'message' => $produit->getNomProduit() . ' ajouté au panier avec succès',
            'count' => $this->getCount($panier)
        ]);
    }

    #[Route('/augmenter/{id}', name: 'panier_augmenter', methods: ['POST'])]
    public function augmenter(Produit $produit, SessionInterface $session): JsonResponse
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
            return new JsonResponse(['success' => false, 'message' => 'Produit épuisé'], 400);
        }

        $panier[$id]['quantite']++;
        $session->set('panier', $panier);

        return new JsonResponse([
            'success' => true,
            'message' => 'Quantité augmentée',
            'quantite' => $panier[$id]['quantite'],
            'count' => $this->getCount($panier)
        ]);
    }

    #[Route('/diminuer/{id}', name: 'panier_diminuer', methods: ['POST'])]
    public function diminuer(Produit $produit, SessionInterface $session): JsonResponse
    {
        $panier = $session->get('panier', []);
        $id = $produit->getId_produit();

        if (!isset($panier[$id])) {
            return new JsonResponse(['success' => false, 'message' => 'Produit introuvable'], 400);
        }

        $panier[$id]['quantite']--;

        if ($panier[$id]['quantite'] <= 0) {
            unset($panier[$id]);
            $session->set('panier', $panier);

            return new JsonResponse([
                'success' => true,
                'message' => 'Produit retiré du panier',
                'count' => $this->getCount($panier)
            ]);
        }

        $session->set('panier', $panier);

        return new JsonResponse([
            'success' => true,
            'message' => 'Quantité diminuée',
            'quantite' => $panier[$id]['quantite'],
            'count' => $this->getCount($panier)
        ]);
    }
    #[Route('/supprimer/{id}', name: 'panier_supprimer', methods: ['POST'])]
    public function supprimer(Produit $produit, SessionInterface $session): JsonResponse
    {
        $panier = $session->get('panier', []);
        $id = $produit->getId_produit();
    
        if (!isset($panier[$id])) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Produit introuvable'
            ], 400);
        }
    
        unset($panier[$id]);
        $session->set('panier', $panier);
    
        return new JsonResponse([
            'success' => true,
            'message' => $produit->getNomProduit() . ' supprimé du panier',
            'count' => $this->getCount($panier)
        ]);
    }
    
    #[Route('/vider', name: 'panier_vider', methods: ['POST'])]
    public function vider(SessionInterface $session): JsonResponse
    {
        $session->remove('panier');

        return new JsonResponse([
            'success' => true,
            'message' => 'Panier vidé avec succès',
            'count' => 0
        ]);
    }

    private function getCount(array $panier): int
    {
        $count = 0;
        foreach ($panier as $item) {
            $count += (int) ($item['quantite'] ?? 0);
        }
        return $count;
    }
}
