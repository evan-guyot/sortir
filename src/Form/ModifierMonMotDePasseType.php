<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;


class ModifierMonMotDePasseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('motdepasse', RepeatedType::class, [
            'required' => false,
            'type' => PasswordType::class,
            'invalid_message' => "Les mots de passe doivent correspondre.",
            'options' => ['attr' => ['class' => 'password-field']],
            'first_options'  => ['label' => 'Nouveau mot de passe :'],
            'second_options' => ['label' => 'Confirmer le mot de passe :']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}

