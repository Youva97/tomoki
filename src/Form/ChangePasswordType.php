<?php

namespace App\Form;

use App\Entity\User;
use Doctrine\DBAL\Types\TextType;
use PharIo\Manifest\Email;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email',EmailType::class,[
                'label' => 'Mon Email',
                'disabled' => true,
            ])
            //->add('roles')
            ->add('oldPassword',PasswordType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Mon mot de passe actuel'
                ]
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Mon prénom',
                'disabled' => true,
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Mon nom',
                'disabled' => true,
            ])
            ->add('newPassword', PasswordType::class,['label' => 'Nouveau mot de passe'])
            ->add('confirmNewPassword', PasswordType::class,['label' => "Confirmation Nouveau Mot de passe"])
            ->add('submit', SubmitType::class,['label' => 'Modifier le mot de passe'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
