<?php

namespace App\Form;

use App\Entity\Participant;
use App\Entity\Site;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class GestionMonProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class, ['attr' => ['class' => 'form-control'],
                'label' => 'Pseudo',
            ])
            ->add('nom', TextType::class, ['attr' => ['class' => 'form-control'],
                'label' => 'Nom',
            ])
            ->add('prenom',TextType::class, ['attr' => ['class' => 'form-control'],
                'label' => 'Prénom',
            ])
            ->add('telephone',TelType::class, [ 'attr' => ['class' => 'form-control'],
                'label' => 'Téléphone',
            ])
            ->add('mail', EmailType::class, [ 'attr' => ['class' => 'form-control'],
                'label' => 'Email',
            ])
            ->add('site', EntityType::class, [ 'attr' => ['class' => 'form-control'],
                'label' => 'Ville de rattachement',
                'class' => Site::class,
                'choice_label' => 'nom',
                'placeholder' => 'Sélectionnez votre ville',
                'required' => true,
            ])
            ->add('maphoto', FileType::class, [ 'attr' => ['class' => 'form-control'],
                'mapped' => false,
                'required' => false,
                'label' => 'Image de profil',
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image au format jpeg ou png',
                    ])
                ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}