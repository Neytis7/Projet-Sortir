<?php

namespace App\Form;

use App\Entity\Sorties;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',null,[
                'label'=>'Nom de la sortie : ',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le nom ne peut pas être vide'
                    ]),
                ],
            ])
            ->add('datedebut',DateTimeType::class,[
                'label'=>'Date et heure de la sortie : ',
                'constraints' => [
                    new Assert\GreaterThanOrEqual([
                        'value' => $options['dateJour'],
                        'message' => 'La date ne peut pas être inférieur a la date du jour'
                    ]),
                ],
            ])
            ->add('duree',null,[
                'label'=>'Durée (minutes) : ',
                'constraints' => [
                    new Assert\PositiveOrZero([
                        'message' => 'On ne peut pas avoir de durée négatif'
                    ]),
                ],
            ])
            ->add('datecloture',null,[
                'label'=>'Date limite d\'inscription : ',
                'constraints' => [
                    //Il faut changer la date du jour par la date de debut
                    new Assert\GreaterThanOrEqual([
                        'value' => $options['dateJour'],
                        'message' => 'La date de cloture doit être supérieur a la date de debut'
                    ]),
                ],
            ])
            ->add('nbinscriptionsmax',null,[
                'label'=>'Nombre de places : ',
                'constraints' => [
                    new Assert\PositiveOrZero([
                        'message' => 'On ne peut pas avoir de nombre négatif'
                    ]),
                ],
            ])
            ->add('descriptioninfos',null,['label'=>'Description et infos : '])
            ->add('lieuxNoLieu',null,['label'=>'Lieux : '])
        ;
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sorties::class,
            'dateJour' => (new \DateTime())->format('d/m/Y h:i:s'),
        ]);
    }
}
