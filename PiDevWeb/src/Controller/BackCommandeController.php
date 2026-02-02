<?php

namespace App\Controller;

use App\Entity\Commande;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/commandes')]
class BackCommandeController extends AbstractController
{
    // ✅ Liste de toutes les commandes (Admin)
    #[Route('/', name: 'admin_commandes_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $commandes = $em->getRepository(Commande::class)->findBy(
            [],
            ['date_creation_commande' => 'DESC']
        );

        return $this->render('back_commande/index.html.twig', [
            'commandes' => $commandes
        ]);
    }

    // ✅ Voir les détails d'une commande
    #[Route('/{id}', name: 'admin_commande_show', methods: ['GET'])]
    public function show(Commande $commande): Response
    {
        return $this->render('back_commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    // ✅ Changer le statut d'une commande
    #[Route('/{id}/statut', name: 'admin_commande_statut', methods: ['POST'])]
    public function changerStatut(
        Request $request, 
        Commande $commande, 
        EntityManagerInterface $em
    ): Response {
        $nouveauStatut = $request->request->get('statut');
        
        $statutsAutorises = ['En attente', 'En cours', 'Expédiée', 'Livrée', 'Annulée'];
        
        if (in_array($nouveauStatut, $statutsAutorises)) {
            $commande->setStatutCommande($nouveauStatut);
            $em->flush();
            
            $this->addFlash('success', 'Statut mis à jour avec succès !');
        } else {
            $this->addFlash('error', 'Statut invalide');
        }

        return $this->redirectToRoute('admin_commande_show', ['id' => $commande->getIdCommande()]);
    }

    // ✅ Supprimer une commande (sans CSRF)
    #[Route('/{id}/delete', name: 'admin_commande_delete', methods: ['POST'])]
    public function delete(
        Commande $commande, 
        EntityManagerInterface $em
    ): Response {
        // Supprimer d'abord les lignes de commande
        foreach ($commande->getLigneCommandes() as $ligne) {
            $em->remove($ligne);
        }
        
        // Ensuite supprimer la commande
        $em->remove($commande);
        $em->flush();
        
        $this->addFlash('success', 'Commande supprimée avec succès');

        return $this->redirectToRoute('admin_commandes_index');
    }
}