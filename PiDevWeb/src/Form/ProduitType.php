<?php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_produit', TextType::class)
            ->add('description_produit', TextareaType::class)
            ->add('prix_produit', NumberType::class)
            ->add('quantite_produit', IntegerType::class)
            ->add('categorie_produit', TextType::class)
            ->add('status_produit', ChoiceType::class, [
                'choices' => [
                    'Disponible' => 'Disponible',
                    'Rupture' => 'Rupture',
                    'Expire' => 'Expire',
                ],
            ])
            ->add('image_produit', TextType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
