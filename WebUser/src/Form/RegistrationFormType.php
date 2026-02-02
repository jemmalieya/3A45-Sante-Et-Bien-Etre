<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('emailUser', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'placeholder' => 'ex: nom.prenom@gmail.com',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir votre email.']),
                ],
            ])

            // ✅ CIN
            ->add('cin', TextType::class, [
                'label' => 'CIN',
                'attr' => [
                    'placeholder' => '8 chiffres',
                    'maxlength' => 8,
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir votre CIN.']),
                    new Length([
                        'min' => 8,
                        'max' => 8,
                        'exactMessage' => 'Le CIN doit contenir exactement {{ limit }} chiffres.',
                    ]),
                    new Regex([
                        'pattern' => '/^\d{8}$/',
                        'message' => 'Le CIN doit contenir uniquement 8 chiffres.',
                    ]),
                ],
            ])

            // ✅ NOM
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => [
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

            // ✅ PRENOM
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'placeholder' => 'Votre prénom',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir votre prénom.']),
                    new Length([
                        'min' => 2,
                        'max' => 100,
                        'minMessage' => 'Le prénom doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le prénom ne doit pas dépasser {{ limit }} caractères.',
                    ]),
                    new Regex([
                        'pattern' => "/^[A-Za-zÀ-ÿ\s'-]+$/u",
                        'message' => "Le prénom ne doit contenir que des lettres.",
                    ]),
                ],
            ])

            // ✅ DATE NAISSANCE
            ->add('dateNaissance', DateType::class, [
                'label' => 'Date de naissance',
                'widget' => 'single_text',
                'attr' => [
                    'max' => (new \DateTime())->format('Y-m-d'), // bloque future dates côté HTML
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir votre date de naissance.']),
                ],
            ])

            // ✅ TELEPHONE
            ->add('telephoneUser', TextType::class, [
                'label' => 'Téléphone',
                'attr' => [
                    'placeholder' => 'ex: 12345678 ou +21612345678',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir votre numéro de téléphone.']),
                    new Regex([
                        'pattern' => '/^\+?\d{8,20}$/',
                        'message' => "Téléphone invalide. Exemple: 12345678 ou +21612345678.",
                    ]),
                ],
            ])

            // ✅ ADRESSE (optionnelle)
            ->add('adresseUser', TextType::class, [
                'label' => 'Adresse',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Adresse (optionnelle)',
                ],
            ])

            // ✅ MOT DE PASSE (obligatoire en inscription)
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Mot de passe',
                'mapped' => false, // important
                'attr' => [
                    'autocomplete' => 'new-password',
                    'placeholder' => 'Au moins 8 caractères + 1 chiffre',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir un mot de passe.']),
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

            // ✅ AGREE TERMS
            ->add('agreeTerms', CheckboxType::class, [
                'label' => "J'accepte les conditions d'utilisation",
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les conditions.',
                    ]),
                ],
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
