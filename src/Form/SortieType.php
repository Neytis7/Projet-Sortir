<?php

namespace App\Form;

use App\Entity\Sorties;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',null,['label'=>'Nom de la sortie : '])
            ->add('datedebut',null,['label'=>'Date et heure de la sortie : '])
            ->add('duree',null,['label'=>'Durée (minutes) : '])
            ->add('datecloture',null,['label'=>'Date limite d\'inscription : '])
            ->add('nbinscriptionsmax',null,['label'=>'Nombre de places : '])
            ->add('descriptioninfos',null,['label'=>'Description et infos : '])
            ->add('lieuxNoLieu',null,['label'=>'Lieux : '])
            ->add('etatsNoEtat',null,['label'=>'Etat de la sortie : '])
        ;
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sorties::class,
        ]);
    }
}
