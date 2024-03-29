<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Quel nom souhaitez-vous donner à votre adresse',
                'required' => false,
                'attr' => [
                    'placeHolder' => "Nommez votre adresse"
                ]
            ])

            ->add('firstName', TextType::class, [
                'label' => 'Votre prénom',
                'required' => false,
                'attr' => [
                    'placeHolder' => "Entrer votre prénom"
                ]
            ])

            ->add('lastName', TextType::class, [
                'label' => 'Votre nom',
                'required' => false,
                'attr' => [
                    'placeHolder' => "Entrer votre nom"
                ]
            ])

            ->add('company', TextType::class, [
                'label' => 'Votre société',
                'required' => false,
                'attr' => [
                    'placeHolder' => "(facultatif) entre le nom de votre société"
                ]
            ])

            ->add('address', TextType::class, [
                'label' => 'Votre adresse',
                'required' => false,
                'attr' => [
                    'placeHolder' => "8 rue des lilas ..."
                ]
            ])

            ->add('postal', TextType::class, [
                'label' => 'Votre code postal',
                'required' => false,
                'attr' => [
                    'placeHolder' => "Entrer votre code postal"
                ]
            ])

            ->add('city', TextType::class, [
                'label' => 'Votre ville',
                'required' => false,
                'attr' => [
                    'placeHolder' => "Entrer votre ville"
                ]
            ])
            ->add('country', CountryType::class, [
                'label' => 'Pays',
                'required' => false,
                'attr' => [
                    'placeHolder' => "Entrer votre pays"
                ]
            ])
            ->add('phone', TelType::class, [
                'label' => 'Votre Téléphone',
                'required' => false,
                'attr' => [
                    'placeHolder' => "Entrez votre téléphone"
                ]
            ])
            /*->add('user', EntityType::class, [
                'class' => User::class,
            'choice_label' => 'id',
            ])*/
            ->add('submit', SubmitType::class, [
                'label' => 'Sauvegarder Votre Addresse',
                'attr' => [
                    'class' => "btn btn-success col-6"
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
