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
        for ($i = 1; $i <= 10; $i++) { 
            $referenceName = 'categorie_' . $i;
            if ($this->hasReference($referenceName)) {
                $categorie = $this->getReference($referenceName);
                $product = new Product(); 
                $product->setCategory($categorie); 
                $product->setName($faker->word(3, true)); 
                $product->setDescription($faker->paragraph(3)); 
                $product->setPrice($faker->numberBetween(10, 200)); 
                $product->setSubtitle($faker->word(3, true)); 
                $slugify = new Slugify(); 
                $product->initializeSlug($slugify->slugify($product->getName()));
                $product->setIllustration($faker->imageUrl(360, 360, 'PRODUCTS')); // images en dur 
                $product->setIllustration($faker->image('C:\laragon/www/tomoki/public/uploads', 360, 360, 'animals', false, true, 'cats', true)); 
                $manager->persist($product); 
            } else {
                // Gérez le cas où la référence n'existe pas
            }
        } 
        $manager->flush(); 
    } 
}
