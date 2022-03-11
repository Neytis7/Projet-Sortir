<?php

namespace App\Form;

use App\Entity\Sortie;
use DateTime;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
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
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
            ->add('dateDebut',DateTimeType::class,[
                'label'=>'Date et heure de la sortie : ',
                'format' => 'd/m/Y h:i:s',
                'model_timezone' => 'Europe/Paris',
                'view_timezone' => 'Europe/Paris',
                'html5' =>false,
                'constraints' => [
                    new Assert\GreaterThanOrEqual([
                        'value' => $options['dateJour'],
                        'message' => 'La date de début ne peut pas être inférieur a la date du jour'
                    ]),
                ],
                'attr' => [
                    'class' => 'form-control'
                ],
                'data' => new DateTime()
            ])
            ->add('duree',null,[
                'label'=>'Durée (minutes) : ',
                'constraints' => [
                    new Assert\Positive([
                        'message' => 'On ne peut pas avoir de durée négatif'
                    ]),
                    new Assert\NotBlank([
                        'message' => 'Il faut obligatoirement une durée'
                    ]),
                ],
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
            //Contrainte sur la date de cloture > date de debut
            ->add('dateCloture',null,[
                'label'=>'Date limite d\'inscription : ',
                'format' => 'd/m/Y h:i:s',
                'model_timezone' => 'Europe/Paris',
                'view_timezone' => 'Europe/Paris',
                'html5' =>false,
                'constraints' => [
                    new Assert\GreaterThanOrEqual([
                        'value' => $options['dateJour'],
                        'message' => 'La date de début ne peut pas être inférieur a la date du jour'
                    ]),
                ],
                'attr' => [
                    'class' => 'form-control'
                ],
                'data' => new DateTime()
            ])
            ->add('nbInscriptionsMax',null,[
                'label'=>'Nombre de places : ',
                'constraints' => [
                    new Assert\PositiveOrZero([
                        'message' => 'On ne peut pas avoir un nombre de place négatif'
                    ]),
                ],
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
            ->add('descriptionInfos',null, [
                    'label'=>'Description et infos : ',
                    'attr' => [
                        'class' => 'form-control'
                    ],
                ]
            )

            ->add('lieu',null,[
                'label'=>'Lieu : ',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Il faut obligatoirement un lieu'
                    ]),
                ],
                'attr' => [
                    'class' => 'form-control'
                ],
            ]);
            $builder->add('image',FileType::class, [
                'label' => 'Ajouter une photo',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Assert\File([
                        'maxSize' => '10000k',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/png',
                            'image/gif',
                            'image/webp',
                            'image/jpeg',
                            'image/JPG',
                            'image/PNG',
                            'image/GIF',
                            'image/WEBP',
                            'image/JPEG',
                        ],
                        'mimeTypesMessage' => 'Vueillez choisir une photo valide',
                    ])
                ],
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
            ->add('prive', ChoiceType::class,[
                'label'=> 'Prive : ',
                'required' => true,
                'choices' => [
                    'Publique' => false,
                    'Privé'    => true,
                ],
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
            ->addEventSubscriber($this)
        ;
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
            'dateJour' => DateTime::createFromFormat('g:iA',(new \DateTime())->setTimezone(new \DateTimeZone('Europe/Paris'))->format('g:iA'),(new \DateTimeZone('Europe/Paris'))),
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
        if ($submittedData->getDateCloture() > $submittedData->getDateDebut()) {
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
