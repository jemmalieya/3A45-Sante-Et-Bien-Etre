<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationAdminType;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\ReponseReclamation;
use App\Form\ReponseReclamationType;
use App\Repository\ReponseReclamationRepository;


#[Route('/admin_reclamation')]
class AdminReclamationController extends AbstractController
{
    #[Route('', name: 'admin_reclamation_list', methods: ['GET'])]
    public function list(ReclamationRepository $repo): Response
    {
        // Option sécurité (si vous avez les rôles) :
        // $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $reclamations = $repo->findBy([], ['date_creation_r' => 'DESC']);

        return $this->render('admin_reclamation/index.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }
     
    #[Route('/{id}/repondre', name: 'admin_reclamation_repondre', methods: ['GET','POST'])]
public function repondre(Request $request, Reclamation $reclamation, EntityManagerInterface $em): Response
{
    $ReponseReclamation = new ReponseReclamation();
    $ReponseReclamation->setReclamation($reclamation);
    $ReponseReclamation->setDateCreationRep(new \DateTimeImmutable());

    $form = $this->createForm(ReponseReclamationType::class, $ReponseReclamation);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        // date modification (optionnel)
        $ReponseReclamation->setDateModificationRep(new \DateTimeImmutable());

        // exemple logique métier : dès qu'on répond, on passe la réclamation en "TRAITEE"
        $reclamation->setStatutReclamation('TRAITEE');

        // date clôture si pas déjà mise
        if ($reclamation->getDateClotureR() === null) {
            $reclamation->setDateClotureR(new \DateTimeImmutable());
        }

        $reclamation->setDateModificationR(new \DateTimeImmutable());

        $em->persist($ReponseReclamation);
        $em->flush();

        $this->addFlash('success', 'Réponse envoyée avec succès.');
        return $this->redirectToRoute('admin_reclamation_list');
    }

    return $this->render('admin_reclamation/repondre.html.twig', [
        'form' => $form->createView(),
        'reclamation' => $reclamation,
    ]);
}
#[Route('/{id}/reponses', name: 'admin_reclamation_reponses', methods: ['GET'])]
public function listReponses(
    Reclamation $reclamation,
    ReponseReclamationRepository $repo
): Response {
    $reponses = $repo->findBy(
        ['reclamation' => $reclamation],
        ['date_creation_rep' => 'DESC']
    );

    return $this->render('admin_reclamation/reponses.html.twig', [
        'reclamation' => $reclamation,
        'reponses' => $reponses,
    ]);
}
#[Route('/reponse/{id}/edit', name: 'admin_reponse_edit', methods: ['GET','POST'])]
public function editReponse(Request $request, ReponseReclamation $reponse, EntityManagerInterface $em): Response
{
    $form = $this->createForm(ReponseReclamationType::class, $reponse);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        // date modification
        $reponse->setDateModificationRep(new \DateTimeImmutable());

        $em->flush();
        $this->addFlash('success', 'Réponse modifiée avec succès.');

        // retour sur la liste des réponses de la réclamation
        $idReclamation = $reponse->getReclamation()->getIdReclamation();
        return $this->redirectToRoute('admin_reclamation_reponses', ['id' => $idReclamation]);
    }

    return $this->render('admin_reclamation/edit_reponse.html.twig', [
        'form' => $form->createView(),
        'reponse' => $reponse,
        'reclamation' => $reponse->getReclamation(),
    ]);
}
#[Route('/reponse/{id}/delete', name: 'admin_reponse_delete', methods: ['POST'])]
public function deleteReponse(Request $request, ReponseReclamation $reponse, EntityManagerInterface $em): Response
{
    if (!$this->isCsrfTokenValid('delete_reponse' . $reponse->getIdReponse(), $request->request->get('_token'))) {
        throw $this->createAccessDeniedException('Token CSRF invalide.');
    }

    $idReclamation = $reponse->getReclamation()->getIdReclamation();

    $em->remove($reponse);
    $em->flush();

    $this->addFlash('success', 'Réponse supprimée.');
    return $this->redirectToRoute('admin_reclamation_reponses', ['id' => $idReclamation]);
}

    
}
