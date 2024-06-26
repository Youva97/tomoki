<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\SearchFilters;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SearchFiltersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('string', TextType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Rechercher'
                ]
            ])
            ->add('categories', EntityType::class, [
                'class' => Category::class, // avec quelle classe faire le lien pour le formulaire ( chercher les propriétés à afficher dans le formulaire) 
                'choice_label' => 'name',
                'required' => false,
                'multiple' => true, // choix de plusieurs valeurs 
                'expanded' => true, // cases à cocher 
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Filtrer',
                'attr' => ['class' => 'btn col-12 btn-primary']
            ]);;;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            'data_class' => SearchFilters::class, 'method' => 'GET'
        ]);
    }
}
