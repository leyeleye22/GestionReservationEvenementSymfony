<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }
    public function load(ObjectManager $manager)
    {
        // Créer une instance de Faker
        $faker = Factory::create('fr_FR');
       
        for ($i = 0; $i < 10; $i++) {
            $user = new User();

            // Utiliser Faker pour générer des données aléatoires
            $user->setNomComplet($faker->name);
            $user->setPseudo($faker->userName);
            $user->setEmail($faker->email);
            $user->setTelephone($faker->phoneNumber);
            $user->setUpdateAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 years', 'now')));
            $user->setRoles(['ROLE_USER']);
   
            $Haspassword=$this->hasher->hashPassword(
                $user,
                'password'
            );
            $user->setPassword($Haspassword);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
