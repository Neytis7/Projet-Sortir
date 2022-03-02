<?php

namespace App\Form;

use App\Entity\Participants;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ProfilType extends AbstractType
{
    const VALUE_CANNOT_BE_NULL = 'La valeur %s doit être renseignée';
    const VALUE_MUST_BE_NUMBER = 'veuillez renseigné un nombre !';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo',TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => sprintf(self::VALUE_CANNOT_BE_NULL, 'pseudo')
                    ])
                ]
            ])

            ->add('nom',TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => sprintf(self::VALUE_CANNOT_BE_NULL, 'nom')
                    ])
                ]
            ])

            ->add('prenom',TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => sprintf(self::VALUE_CANNOT_BE_NULL, 'prenom')
                    ])
                ]
            ])

            ->add('telephone',TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => false,

            ])

            ->add('mail',TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => sprintf(self::VALUE_CANNOT_BE_NULL, 'mail')
                    ])
                ]
            ])

            ->add('motDePasse', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => [
                    'attr' => [
                        'class' => 'password-field form-control',
                    ]
                ],
                'required' => true,
                'first_options'  => [
                    'label' => 'mot de passe'
                ],
                'second_options' => [
                    'label' => 'comfirmer mot de passe'
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => sprintf(self::VALUE_CANNOT_BE_NULL, 'mot de passe')
                    ])
                ]
            ])

            ->add('sitesNoSite', IntegerType::class, [
                'attr' => [
                    'label' => 'site',
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => sprintf(self::VALUE_CANNOT_BE_NULL, 'site')
                    ]),
                    new Assert\Positive([
                        'message' => self::VALUE_MUST_BE_NUMBER
                    ])
                ]
            ])

            ->add('photo', TextType::class, [
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ],
            ])

            ->add('administrateur', CheckboxType::class,[
                'required' => false
            ])

            ->add('actif', CheckboxType::class,[
                'required' => false
            ])

            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-success'
                ]
            ]);            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participants::class,
        ]);
    }
}
