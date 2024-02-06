<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

class AjoutSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, ['attr' => ['class' => 'form-control'], 'label' => 'Nom de la sortie', 'constraints' => [
                new NotBlank([
                    'message' => 'Renseignez un nom à votre sortie.',
                ])
            ]])
            ->add('datedebut', DateTimeType::class, ['attr' => ['class' => 'form-control'], 'label' => 'Date & heure de la sortie', 'constraints' => [
                new NotBlank([
                    'message' => 'Une date de début est requise.',
                ]),
                new GreaterThan(['value' => new \DateTime(), 'message' => 'La date de début ne peut être un moment passé.'])
            ]])
            ->add('datecloture', DateType::class, ['attr' => ['class' => 'form-control'], 'label' => 'Date limite d\'inscription', 'constraints' => [
                new NotBlank([
                    'message' => 'Une date de cloture est requise.',
                ]),
                new GreaterThan(['value' => date("Y-m-d H:i:s"), 'message' => 'La date de cloture ne peut être aujourd\'hui ou un jour qui précède.'])
            ]])
            ->add('inscriptionsmax', IntegerType::class, ['label' => 'Nombre de places',
                'attr' => [
                    'min' => 1,
                    'class' => 'form-control'
                ],
                'constraints' => [new NotBlank([
                    'message' => 'Un nombre d\'inscriptions max est requis.',
                ]),
                    new GreaterThan([
                        'value' => 0,
                        'message' => 'Le nombre d\'inscrits doit être supérieur à 0.'])
                ]])
            ->add('duree', IntegerType::class, ['label' => 'Durée',
                'attr' => [
                    'min' => 0,
                    'class' => 'form-control'
                ], 'constraints' => [new NotBlank([
                    'message' => 'Une durée est requise.',
                ]),
                    new GreaterThan([
                        'value' => 0,
                        'message' => 'La durée doit être supérieur à 0 minutes.'])
                ]])
            ->add('description', TextareaType::class, ['attr' => ['class' => 'form-control'],'label' => 'Description & infos'])
            ->add('lieu', EntityType::class, [
                "class" => Lieu::class,
                "choice_label" => "nom",
                'attr' => ['class' => 'form-control']
            ])
            ->add('enregister', SubmitType::class, ['attr' => ['class' => 'form-control btn btn-success'], 'label' => 'Enregistrer'])
            ->add('publier', SubmitType::class, ['attr' => ['class' => 'form-control btn btn-primary'], 'label' => 'Publier'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
