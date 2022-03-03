<?php

namespace App\Form;

use App\Entity\Participant;
use App\Entity\Site;
use App\Form\DataTransformer\SiteTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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

    private $transformer;

    public function __construct(SiteTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isAdmin = !$options['isAdmin'];
        $sitesChoices = $options['sites_choices'];
        $participant = $options['participant'];
        $disabled = $options['disabled'];

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
            ]);

            if (!$disabled) {
                $builder->add('site', ChoiceType::class, [
                    'attr' => [
                        'class' => 'form-control'
                    ],
                    'constraints' => [
                        new Assert\NotBlank([
                            'message' => sprintf(self::VALUE_CANNOT_BE_NULL, 'site')
                        ])
                    ],
                    'choices' => $sitesChoices,
                    'choice_value' => 'id',
                    'choice_label' => function(?Site $site) {
                        return $site
                            ? $site->getNom()
                            : '';
                    },
                ]);
            } else {
                $builder->add('site', TextType::class, [
                    'attr' => [
                        'class' => 'form-control'
                    ],
                    'constraints' => [
                        new Assert\NotBlank([
                            'message' => sprintf(self::VALUE_CANNOT_BE_NULL, 'site')
                        ])
                    ],
                    'data' => !is_null($participant)
                        ? !is_null($participant->getSite())
                            ? $participant->getSite()->getNom()
                            : 'site non renseigné'
                        : 'site non renseigné'
                ]);
            }


            $builder->add('administrateur', CheckboxType::class,[
                'required' => false,
                'attr' => array(
                    'disabled' => $isAdmin,
                ),
            ])

            ->add('actif', CheckboxType::class,[
                'required' => false,
                'attr' => array(
                    'disabled' => $isAdmin,
                ),
            ])

            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-success'
                ]
            ]);

        if(!$disabled) {
            $builder->get('site')
                ->addModelTransformer($this->transformer);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
            'isAdmin' => false,
            'sites_choices' => [],
            'disabled' => false,
            'participant' => null,
        ]);
    }
}
