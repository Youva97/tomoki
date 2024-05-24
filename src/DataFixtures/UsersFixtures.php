<?php

namespace App\DataFixtures;
use Faker\Factory;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager; 
use Doctrine\Bundle\FixturesBundle\Fixture; 
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface; 

class UsersFixtures extends Fixture {
    
    private $passwordHasher; 
    
    public function __construct(UserPasswordHasherInterface $passwordHasher) { 
        $this->passwordHasher = $passwordHasher; 
    }

    public function load(ObjectManager $manager) {
        $admin = new User();
        $admin->setFirstName('Gael')
        ->setLastName('Minéas') 
        ->setEmail('gael@test.com') 
        ->setRoles(["ROLE_ADMIN"])
        ->setActive(1);
        $admin->setPassword($this->passwordHasher->hashPassword( $admin, 'test' )); 
        $manager->persist($admin); $faker = Factory::create('fr_FR'); 
        //dump($faker->name); 
        //dump($faker->address); 
        for ($i = 1; $i < 5; $i++) { 
            $user = new User();
            $user->setFirstName($faker->firstName());
            $user->setLastName($faker->lastName());
            $user->setEmail($faker->email());
            $user->setActive(0);
            $user->setPassword($this->passwordHasher->hashPassword( $user, 'test' ));
            $manager->persist($user);
        } 
        $manager->flush();
    } 
}