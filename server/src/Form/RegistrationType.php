<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'login',
                TextType::class,
                self::getConfiguration('Login', 'Votre Login')
            )
            ->add(
                'hash',
                PasswordType::class,
                self::getConfiguration('Password', 'Choissisez un bon mot de passe.')
            )
            ->add(
                'passwordConfirm',
                PasswordType::class,
                self::getConfiguration(
                    'Confirmation de mot de passe',
                    'Veuillez confirmer votre mot de passe.'
                )
            )
            ->add(
                'save',
                SubmitType::class,
                [
                    'attr' => [
                        'class' => 'btn btn-success',
                    ],
                    'label' => 'Confirmer l\'inscription',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => User::class,
            ]
        );
    }
}
