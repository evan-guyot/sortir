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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

class AjoutSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, ['label' => 'Nom de la sortie', 'constraints' => [
                new NotBlank([
                    'message' => 'Renseignez un nom à votre sortie.',
                ])
            ]])
            ->add('datedebut', DateTimeType::class, ['label' => 'Date & heure de la sortie', 'constraints' => [
                new NotBlank([
                    'message' => 'Une date de début est requise.',
                ]),
                new GreaterThan(['value' => new \DateTime(), 'message' => 'La date de début ne peut être un moment passé.'])
            ]])
            ->add('datecloture', DateType::class, ['label' => 'Date limite d\'inscription', 'constraints' => [
                new NotBlank([
                    'message' => 'Une date de cloture est requise.',
                ]),
                new GreaterThan(['value' => date("Y-m-d H:i:s"), 'message' => 'La date de cloture ne peut être aujourd\'hui ou un jour qui précède.'])
            ]])
            ->add('inscriptionsmax', IntegerType::class, ['label' => 'Nombre de places', 'constraints' => [
                new NotBlank([
                    'message' => 'Un nombre d\'inscriptions max est requis.',
                ])
            ]])
            ->add('duree', IntegerType::class, ['label' => 'Durée', 'constraints' => [
                new NotBlank([
                    'message' => 'Une durée est requise.',
                ])
            ]])
            ->add('description', TextareaType::class, ['label' => 'Description & infos'])
            ->add('urlphoto', FileType::class, ['label' => ' ', 'required' => false])
            ->add('lieu', EntityType::class, [
                "class" => Lieu::class,
                "choice_label" => "nom"
            ])
            ->add('enregister', SubmitType::class, ['label' => 'Enregistrer'])
            ->add('publier', SubmitType::class, ['label' => 'Publier'])
            ->add('annuler', ButtonType::class, ['label' => 'Annuler']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
