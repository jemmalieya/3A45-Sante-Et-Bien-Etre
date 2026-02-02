<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ✅ CIN
            ->add('cin', TextType::class, [
                'label' => 'CIN',
                'required' => false,
                'attr' => [
                    'placeholder' => '8 chiffres',
                ],
                'constraints' => [
                    new Length([
                        'min' => 8,
                        'max' => 8,
                        'exactMessage' => 'Le CIN doit contenir exactement {{ limit }} chiffres.',
                    ]),
                    new Regex([
                        'pattern' => '/^\d{8}$/',
                        'message' => 'Le CIN doit contenir uniquement des chiffres.',
                    ]),
                ],
            ])

            // ✅ NOM
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank(['message' => 'Le nom est obligatoire.']),
                    new Length([
                        'min' => 2,
                        'max' => 100,
                    ]),
                    new Regex([
                        'pattern' => "/^[A-Za-zÀ-ÿ\s'-]+$/u",
                        'message' => 'Le nom ne doit contenir que des lettres.',
                    ]),
                ],
            ])

            // ✅ PRENOM
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank(['message' => 'Le prénom est obligatoire.']),
                    new Length([
                        'min' => 2,
                        'max' => 100,
                    ]),
                    new Regex([
                        'pattern' => "/^[A-Za-zÀ-ÿ\s'-]+$/u",
                        'message' => 'Le prénom ne doit contenir que des lettres.',
                    ]),
                ],
            ])

            // ✅ DATE NAISSANCE
            ->add('dateNaissance', DateType::class, [
                'label' => 'Date de naissance',
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank(['message' => 'La date de naissance est obligatoire.']),
                ],
            ])

            // ✅ TELEPHONE
            ->add('telephoneUser', TextType::class, [
                'label' => 'Téléphone',
                'constraints' => [
                    new NotBlank(['message' => 'Le numéro de téléphone est obligatoire.']),
                    new Regex([
                        'pattern' => '/^\+?\d{8,20}$/',
                        'message' => 'Numéro de téléphone invalide.',
                    ]),
                ],
            ])

            // ✅ EMAIL
            ->add('emailUser', EmailType::class, [
                'label' => 'Email',
                'constraints' => [
                    new NotBlank(['message' => 'Email obligatoire.']),
                ],
            ])

            // ✅ ADRESSE
            ->add('adresseUser', TextType::class, [
                'label' => 'Adresse',
                'required' => false,
            ])

            // ✅ PASSWORD (ADMIN)
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'constraints' => [
                    new NotBlank(['message' => 'Le mot de passe est obligatoire.']),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères.',
                    ]),
                    new Regex([
                        'pattern' => "/^(?=.*[A-Za-z])(?=.*\d).+$/",
                        'message' => 'Le mot de passe doit contenir au moins une lettre et un chiffre.',
                    ]),
                ],
            ])

            // ✅ STATUT COMPTE
            ->add('statutCompte', ChoiceType::class, [
                'label' => 'Statut du compte',
                'required' => false,
                'choices' => [
                    'Actif' => 'ACTIVE',
                    'Restreint' => 'RESTRICTED',
                    'Suspendu' => 'SUSPENDED',
                ],
                'placeholder' => 'Choisir un statut',
            ])

            // ✅ DERNIERE CONNEXION (lecture/admin)
            ->add('derniereConnexion', DateTimeType::class, [
                'label' => 'Dernière connexion',
                'widget' => 'single_text',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
