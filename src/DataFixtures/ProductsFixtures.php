<?php

namespace App\DataFixtures; 
use App\Entity\Product; 
use Faker\Factory; 
use Doctrine\Persistence\ObjectManager; 
use Doctrine\Bundle\FixturesBundle\Fixture; 
use Cocur\Slugify\Slugify;

class ProductsFixtures extends Fixture { 
    public function load(ObjectManager $manager) { 
        $faker = Factory::create('fr_FR'); // création des produits 
        for ($i = 1; $i < 5; $i++) { // on récupère l'objet catégorie (qui ont été crées 
            $categorie = $this->getReference('categorie_' . $faker->numberBetween(1, 10)); 
            $product = new Product(); 
            $product->setCategory($categorie); 
            $product->setName($faker->word(3, true)); 
            $product->setDescription($faker->paragraph(2)); 
            $product->setPrice($faker->numberBetween(10, 200)); 
            $product->setSubtitle($faker->word(3, true)); 
            $slugify = new Slugify(); 
            $product->initializeSlug($slugify->slugify($product->getName()));
            $product->setIllustration($faker->imageUrl(360, 360, 'PRODUCTS')); // images en dur 
            $product->setIllustration($faker->image('C:\laragon/www/tomoki/public/uploads', 360, 360, 'animals', false, true, 'cats', true)); 
            $manager->persist($product); } $manager->flush(); 
        } 
    }