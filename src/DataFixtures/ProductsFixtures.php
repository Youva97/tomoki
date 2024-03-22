<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Product;
use App\Entity\Category;
use Cocur\Slugify\Slugify;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ProductsFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR'); // création des produits 
        for ($i = 1; $i <= 10; $i++) {

            if (!$this->hasReference('categorie_' . $i)) {
                // Si la référence n'existe pas, créez une nouvelle catégorie avec Faker
                $categorie = new Category();
                $categorie->setName($faker->word);
                // Autres attributs de la catégorie...

                // Persistez la nouvelle catégorie
                $manager->persist($categorie);

                // Enregistrez la référence pour une utilisation ultérieure
                $this->setReference('categorie_' . $i, $categorie);
            } else {
                // Si la référence existe déjà, récupérez-la
                $categorie = $this->getReference('categorie_' . $i);
            }

            // Utilisez la référence créée ou récupérée pour associer chaque produit à une catégorie spécifique
            $product = new Product();
            $product->setCategory($categorie);
            // Autres attributs du produit...
            $product->setName($faker->word(3, true));
            $product->setDescription($faker->paragraph(3));
            $product->setPrice($faker->numberBetween(10, 200));
            $product->setSubtitle($faker->word(3, true));
            $slugify = new Slugify();
            $product->initializeSlug($slugify->slugify($product->getName()));
            $product->setIllustration($faker->imageUrl(360, 360, 'PRODUCTS')); // images en dur 
            $product->setIllustration($faker->image('C:\laragon/www/tomoki/public/uploads', 360, 360, 'animals', false, true, 'cats', true));
            $manager->persist($product);
        }
        $manager->flush();
    }
}
