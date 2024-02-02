<?php

namespace App\Form;

use App\Entity\Participant;
use App\Entity\Site;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class, ['attr' => ['class' => 'form-control'], 'label' => 'Pseudo','constraints' => [
                new NotBlank([
                    'message' => 'Un pseudo est requis.',
                ])] ])
            ->add('nom', TextType::class, ['attr' => ['class' => 'form-control'], 'label' => 'Nom','constraints' => [
                new NotBlank([
                    'message' => 'Un nom est requis.',
                ])]])
            ->add('prenom', TextType::class, ['attr' => ['class' => 'form-control'], 'label' => 'Prénom','constraints' => [
                new NotBlank([
                    'message' => 'Un prénom est requis.',
                ])]])
            ->add('telephone', TextType::class, ['attr' => ['class' => 'form-control'], 'label' => 'Téléphone','constraints' => [
                new NotBlank([
                    'message' => 'Un numéro de téléphone est requis.',
                ])]])
            ->add('mail', TextType::class, ['attr' => ['class' => 'form-control'], 'label' => 'E-mail','constraints' => [
                new NotBlank([
                    'message' => 'Un e-mail est requis.',
                ])]])
            ->add('motdepasse', PasswordType::class, ['attr' => ['class' => 'form-control'], 'label' => 'Mot de passe','constraints' => [
                new NotBlank([
                    'message' => 'Un mot de passe est requis.',
                ]),
                new Length([
                    'min' => 6,
                    'minMessage' => 'Vote mot de passe doit au moins contenir 6 caractères',
                    'max' => 64,
                ])]])
            ->add('administrateur', CheckboxType::class, ['attr' => ['class' => 'form-control'], 'label' => 'Administrateur'])
            ->add('actif', CheckboxType::class, ['attr' => ['class' => 'form-control'], 'label' => 'Actif'])
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'nom',
                'label' => 'Pseudo',
                'attr' => ['class' => 'form-control'],'constraints' => [
                    new NotBlank([
                        'message' => 'Un site est requis.',
                    ])]
            ])
            ->add('confirm', SubmitType::class, ['label' => 'Confirmer']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
