<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            /*             ->add('email')
            ->add('roles')
            ->add('firstName')
            ->add('lastName')
            ->add('password') */
            ->add('newPassword', PasswordType::class, ['label' => "Nouveau Mot de passe", "attr" => ['placeholder' => "Entrez votre nouveau mot de passe"]])
            ->add('confirmNewPassword', PasswordType::class, ['label' => "Confirmation Nouveau Mot de passe", "attr" => ['placeholder' => "Confirmé votre nouveau mot de passe"]])
            ->add('submit', SubmitType::class, ['label' => 'Modifier le mot de passe', "attr" => ['class' => 'col-5 btn btn-success text-center']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
