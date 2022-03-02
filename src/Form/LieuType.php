<?php

namespace App\Form;

use App\Entity\Lieux;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class LieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomLieu',null,[
                'label'=>'Nom du lieu : ',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le nom ne peut pas être vide'
                    ]),
                ],
            ])
            ->add('rue',null,[
                'label'=>'Rue : ',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'La rue ne peut pas être vide'
                    ]),
                ],
            ])
            ->add('latitude',null,['label'=>'Latitude : '])
            ->add('longitude',null,['label'=>'Longitude : '])
            ->add('villesNoVille',null,[
                'label'=>'Ville : ',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Il faut obligatoirement une ville'
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lieux::class,
        ]);
    }
}
