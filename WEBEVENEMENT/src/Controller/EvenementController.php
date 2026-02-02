<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Form\EvenementType;
use App\Repository\EvenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EvenementController extends AbstractController
{
    // ✅ FRONT LIST
    #[Route('/evenements', name: 'app_evenements', methods: ['GET'])]
    public function index(EvenementRepository $repo): Response
    {
        $evenements = $repo->findBy([], ['date_debut_event' => 'DESC']);

        return $this->render('evenement/index.html.twig', [
            'evenements' => $evenements,
        ]);
    }

    // ✅ FRONT SHOW
    #[Route('/evenements/{id}', name: 'app_evenement_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Evenement $evenement): Response
    {
        return $this->render('evenement/show.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    // ✅ ADMIN LIST TABLE
    #[Route('/admin/evenements', name: 'admin_evenement_index', methods: ['GET'])]
    public function adminIndex(EvenementRepository $repo): Response
    {
        $evenements = $repo->findBy([], ['date_debut_event' => 'DESC']);

        return $this->render('evenement/admin_index.html.twig', [
            'evenements' => $evenements,
        ]);
    }

    // ✅ ADMIN ADD
    #[Route('/admin/evenements/new', name: 'admin_evenement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $evenement = new Evenement();
        $evenement->setDateCreationEvent(new \DateTime());
        $evenement->setDateMiseAJourEvent(new \DateTime());

        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $evenement->setDateMiseAJourEvent(new \DateTime());
            $em->persist($evenement);
            $em->flush();

            $this->addFlash('success', 'Événement ajouté avec succès ✅');
            return $this->redirectToRoute('admin_evenement_index');
        }

        return $this->render('evenement/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // ✅ ADMIN EDIT
    #[Route('/admin/evenements/{id}/edit', name: 'admin_evenement_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, Evenement $evenement, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $evenement->setDateMiseAJourEvent(new \DateTime());
            $em->flush();

            $this->addFlash('success', 'Événement modifié ✅');
            return $this->redirectToRoute('admin_evenement_index');
        }

        return $this->render('evenement/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form->createView(),
        ]);
    }

    // ✅ ADMIN DELETE
    #[Route('/admin/evenements/{id}/delete', name: 'admin_evenement_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, Evenement $evenement, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_evenement_'.$evenement->getId(), $request->request->get('_token'))) {
            $em->remove($evenement);
            $em->flush();
            $this->addFlash('success', 'Événement supprimé 🗑️');
        }

        return $this->redirectToRoute('admin_evenement_index');
    }
}
