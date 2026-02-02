<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\UserProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
     private UserPasswordHasherInterface $passwordHasher;

    // Inject the password hasher service into the constructor
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    // Show all users
    #[Route('/users', name: 'admin_users')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(User::class)->findAll();
        return $this->render('back_user/admin/user/index.html.twig', [
            'users' => $users,
        ]);
    }

// Edit user profile
    #[Route('/user/{id}/edit', name: 'admin_user_edit')]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // If password is set, hash it using the injected password hasher service
            if ($user->getPlainPassword()) {
                $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPlainPassword());
                $user->setPassword($hashedPassword);
            }

            $entityManager->flush();
            $this->addFlash('success', 'Profile updated successfully');
            return $this->redirectToRoute('admin_users');
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }


    // Delete user
    #[Route('/user/{id}/delete', name: 'admin_user_delete')]
    public function delete(User $user, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($user);
        $entityManager->flush();
        $this->addFlash('success', 'User deleted successfully');
        return $this->redirectToRoute('admin_users');
    }

    // Toggle user restriction (Activate/Deactivate)
    #[Route('/user/{id}/restrict', name: 'admin_user_restrict')]
    public function restrict(User $user, EntityManagerInterface $entityManager): Response
    {
        $user->setStatutCompte(!$user->getStatutCompte());
        $entityManager->flush();
        $this->addFlash('success', 'User status updated');
        return $this->redirectToRoute('admin_users');
    }
}
