<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BackUserController extends AbstractController
{
    #[Route('/back/user', name: 'app_back_user')]
    public function index(): Response
    {
        return $this->render('back_user/index.html.twig', [
            'controller_name' => 'BackUserController',
        ]);
    }
}
