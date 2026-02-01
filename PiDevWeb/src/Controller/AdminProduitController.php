<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/produits')]
class AdminProduitController extends AbstractController
{
    // LISTE DES PRODUITS
    #[Route('', name: 'admin_produit_index', methods: ['GET'])]
    public function index(ProduitRepository $repo): Response
    {
        $produits = $repo->findAll();

        return $this->render('admin_produit/index.html.twig', [
            'produits' => $produits,
        ]);
    }

    // AJOUTER UN PRODUIT
    #[Route('/new', name: 'admin_produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($produit);
            $em->flush();

            $this->addFlash('success', 'Produit ajouté avec succès !');

            return $this->redirectToRoute('admin_produit_index');
        }

        return $this->render('admin_produit/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // MODIFIER UN PRODUIT
    #[Route('/{id}/edit', name: 'admin_produit_edit', methods: ['GET', 'POST'])]
    public function edit(Produit $produit, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Produit modifié avec succès !');

            return $this->redirectToRoute('admin_produit_index');
        }

        return $this->render('admin_produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    // SUPPRIMER UN PRODUIT
    #[Route('/{id}/delete', name: 'admin_produit_delete', methods: ['POST'])]
    public function delete(Produit $produit, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId_produit(), $request->request->get('_token'))) {
            $em->remove($produit);
            $em->flush();

            $this->addFlash('success', 'Produit supprimé avec succès !');
        }

        return $this->redirectToRoute('admin_produit_index');
    }
}