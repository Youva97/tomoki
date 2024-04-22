<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    // faire le style de bootstrap pour le formulaire d'inscription de l'utilisateur
    {
        $builder
            ->add('firstName', TextType::class, ['label' => false, 'attr' => ['placeholder' => 'Votre prénom', 'title' => 'Prénom avec un minimum de 3 caractères et au maximum 20 caractères']])
            ->add('lastName', TextType::class, ['label' => false, 'attr' => ['placeholder' => "Entrez votre nom"]])
            ->add('email', EmailType::class, ['label' => false, 'attr' => ['placeholder' => "Votre email"]])
            // ->add('roles')
            ->add('password', PasswordType::class, ['label' => false, 'attr' => ['placeholder' => "Votre mot de passe"]])
            ->add('confirmPassword', PasswordType::class, ['label' => false, 'attr' => ['placeholder' => "Confirmé votre mot de passe"]])
            ->add('submit', SubmitType::class, ['label' => 'S\'inscrire', 'attr' => ['class' => 'btn btn-success col-6 position-relative top-100 start-50 translate-middle']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['register']
        ]);
    }
}
