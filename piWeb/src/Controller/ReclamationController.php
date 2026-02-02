<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ReclamationRepository;


class ReclamationController extends AbstractController
{
    #[Route('/reclamation', name: 'reclamation_index')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $reclamation = new Reclamation();

        // valeurs automatiques
        $reclamation->setDateCreationR(new \DateTimeImmutable());
        $reclamation->setStatutReclamation('En attente');

        if (!$reclamation->getReferenceReclamation()) {
            $reclamation->setReferenceReclamation(
                'REC-' . date('Ymd') . '-' . rand(1000, 9999)
            );
        }

        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($reclamation);
            $em->flush();

            $this->addFlash('success', 'Réclamation ajoutée avec succès');

            return $this->redirectToRoute('reclamation_index');
        }

        return $this->render('reclamation/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

     #[Route('/reclamation/list', name: 'reclamation_list', methods: ['GET'])]
    public function list(ReclamationRepository $reclamationRepository): Response
    {
        $reclamations = $reclamationRepository->findBy([], ['date_creation_r' => 'DESC']);

        return $this->render('reclamation/list.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }

#[Route('/reclamation/{id}/edit', name: 'reclamation_edit')]
public function edit(
    Request $request,
    Reclamation $reclamation,
    EntityManagerInterface $em
): Response {

    $form = $this->createForm(ReclamationType::class, $reclamation);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        $reclamation->setDateModificationR(new \DateTimeImmutable());

        $em->flush();

        $this->addFlash('success', 'Réclamation modifiée avec succès');

        return $this->redirectToRoute('reclamation_list');
    }

    return $this->render('reclamation/edit.html.twig', [
        'form' => $form,
        'reclamation' => $reclamation,
    ]);
}

#[Route('/reclamation/{id}', name: 'reclamation_delete', methods: ['POST'])]
public function delete(
    Request $request,
    Reclamation $reclamation,
    EntityManagerInterface $em
): Response {
    if ($this->isCsrfTokenValid('delete'.$reclamation->getIdReclamation(), $request->request->get('_token'))) {
        $em->remove($reclamation);
        $em->flush();

        $this->addFlash('success', 'Réclamation supprimée avec succès');
    }

    return $this->redirectToRoute('reclamation_list');
}

}
