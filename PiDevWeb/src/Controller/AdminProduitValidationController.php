<?php

namespace App\Controller;

use App\Entity\Produit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/admin/produits/validate')]
class AdminProduitValidationController extends AbstractController
{
    #[Route('/{field}', name: 'admin_produit_validate', methods: ['POST'])]
    public function validateField(string $field, Request $request, ValidatorInterface $validator): JsonResponse
    {
        $value = $request->request->get('value');
        $produit = new Produit();

        switch ($field) {
            case 'nomProduit':
                $produit->setNomProduit($value);
                break;
            case 'prixProduit':
                $produit->setPrixProduit((float)$value);
                break;
            case 'quantiteProduit':
                $produit->setQuantiteProduit((int)$value);
                break;
            case 'imageProduit':
                $produit->setImageProduit($value);
                break;
            case 'statusProduit':
                $produit->setStatusProduit($value);
                break;
            default:
                return $this->json(['valid' => false, 'message' => 'Champ inconnu']);
        }

        $errors = $validator->validate($produit);

        foreach ($errors as $error) {
            if ($error->getPropertyPath() === $field) {
                return $this->json([
                    'valid' => false,
                    'message' => $error->getMessage()
                ]);
            }
        }

        return $this->json(['valid' => true]);
    }
}
