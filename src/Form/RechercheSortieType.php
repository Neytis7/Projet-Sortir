<?php

namespace App\Form;

use App\Entity\Sorties;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RechercheSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('lieuxNoLieu',null,
            ['label'=>'Site : ',
            'attr' => [
                'class' => 'form-control'
            ]])
        ->add('nom',null,['label'=>'Nom de la sortie contient : ',
            'attr' => [
                'class' => 'form-control'
            ]])
        ->add('datedebut',null,['label'=>'Entre : ',
            'attr' => [
                'class' => 'form-control'
            ]])
        ->add('datecloture',null,['label'=>'et : ',
            'attr' => [
                'class' => 'form-control'
            ]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sorties::class,
        ]);
    }
}
