<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\Carrier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];
        $builder
            ->add('addresses', EntityType::class, [
                'label' => 'Choisissez votre addresse de livraison',
                'required' => true,
                'class' => Address::class,
                'multiple' => false,
                'expanded' => true,
                'choices' => $user->getAddresses(),
            ])
            ->add('transporteurs', EntityType::class, [
                'label' => 'Choisissez votre transporteur',
                'required' => true,
                'class' => Carrier::class,
                'multiple' => false,
                'expanded' => true
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Validez ma commande',
                'attr' => [
                    'class' => 'col-12 btn btn-success'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'user' => [],
            // Configure your form options here
        ]);
    }
}
