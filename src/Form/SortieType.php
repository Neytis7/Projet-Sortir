<?php

namespace App\Form;

use App\Entity\Sorties;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class SortieType extends AbstractType implements EventSubscriberInterface
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
            //Contrainte sur la date de cloture > date de debut
            ->add('datecloture',null,['label'=>'Date limite d\'inscription : ',
                'constraints' => [
                    new Assert\GreaterThan([
                        'value' => $options['dateJour'],
                        'message' => 'La date ne peut pas être inférieur a la date du jour'
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
            ->addEventSubscriber($this)
        ;
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sorties::class,
            'dateJour' => (new \DateTime())->format('d/m/Y h:i:s'),
        ]);
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::SUBMIT => 'ensureOneFieldIsSubmitted',
        ];
    }

    public function ensureOneFieldIsSubmitted(FormEvent $event)
    {
        $submittedData = $event->getData();
        // just checking for null here, but you may want to check for an empty string or something like that
        if ($submittedData->getDatecloture() > $submittedData->getDatedebut()) {
            throw new TransformationFailedException(
                'La date de cloture ne peut pas être supérieur à la date de debut',
                0, // code
                null, // previous
                'La date de cloture ne peut pas être supérieur à la date de debut', // user message
                ['{{ whatever }}' => 'here'] // message context for the translater
            );
        }
    }
}
