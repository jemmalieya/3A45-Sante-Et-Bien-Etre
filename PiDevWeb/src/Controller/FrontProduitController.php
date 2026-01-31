<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/pharmacie')]
class FrontProduitController extends AbstractController
{
    #[Route('', name: 'front_produit_index', methods: ['GET'])]
    public function index(ProduitRepository $repo): Response
    {
        // Récupère tous les produits depuis la base
        $produits = $repo->findAll();

        return $this->render('front_produit/index.html.twig', [
            'produits' => $produits
        ]);
    }
}
