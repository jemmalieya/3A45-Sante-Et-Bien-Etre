<?php
// src/Form/UserProfileType.php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class UserProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('emailUser', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'ex: nom.prenom@gmail.com',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir votre email.']),
                ],
            ])

            // ✅ nom (ou username)
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Votre nom',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir votre nom.']),
                    new Length([
                        'min' => 2,
                        'max' => 100,
                        'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le nom ne doit pas dépasser {{ limit }} caractères.',
                    ]),
                    new Regex([
                        'pattern' => "/^[A-Za-zÀ-ÿ\s'-]+$/u",
                        'message' => "Le nom ne doit contenir que des lettres.",
                    ]),
                ],
            ])

            // ✅ plainPassword optionnel: si rempli => règles
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Nouveau mot de passe',
                'mapped' => false,          // IMPORTANT : sinon Symfony va chercher un champ en DB
                'required' => false,        // optionnel
                'attr' => [
                    'class' => 'form-control',
                    'autocomplete' => 'new-password',
                    'placeholder' => "Laisser vide pour ne pas changer",
                ],
                'constraints' => [
                    // ⚠️ PAS de NotBlank ici (car edit)
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères.',
                        'max' => 255,
                    ]),
                    new Regex([
                        'pattern' => "/^(?=.*[A-Za-z])(?=.*\d).+$/",
                        'message' => "Le mot de passe doit contenir au moins une lettre et un chiffre.",
                    ]),
                ],
            ])

        ;
    }

  public function configureOptions(OptionsResolver $resolver): void
{
    $resolver->setDefaults([
        'data_class' => User::class,
        'validation_groups' => ['profile_edit'],
    ]);
}

}
