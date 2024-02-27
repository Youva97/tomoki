<?php

namespace App\DataFixtures;

use Faker\Factory;
use Cocur\Slugify\Slugify;
use Gedmo\Mapping\Annotation as Gedmo;
use App\Entity\Product;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        // $product = new Product();
        // $manager->persist($product);
        for ($i=0; $i < 5 ; $i++) { 
            $category = new Category();
            $category->setName('Métaux'.$i);
            $name = ($faker->word(3, true));
            $slugify = new Slugify();
            $manager->persist($category);
            
            for ($jk = 1; $jk < 5; $jk++) {
                // on récupère l'objet catégorie (qui ont été crées 
                $product = new Product(); $product->setCategory($category); 
                $product->setName($name);
                $product->setDescription($faker->paragraph(2));
                $product->setPrice($faker->numberBetween(1000, 20000)); 
                $product->setSubtitle($faker->word(3, true)); 
                $product->initializeSlug($slugify->slugify($product->getName()));
                // images sous forme de lien 
                // $product->setIllustration($faker->imageUrl(360, 360, 'PRODUCTS')); 
                // images en dur 
                $product->setIllustration($faker->image('C:\laragon\www\tomoki\public\uploads', 360, 360, 'animals',false, true, 'cats', true)); 
                $manager->persist($product); 
            } 
        }
        $manager->flush();
    }
}
