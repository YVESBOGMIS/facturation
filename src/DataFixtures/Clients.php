<?php

namespace App\DataFixtures;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\Clients;

class ClientsFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 10; $i++) {
            $client = new Clients();
            $client->setNom($faker->lastName);
            $client->setPrenom($faker->firstName);
            $client->setEmail($faker->email);
            $client->setTelephone($faker->phoneNumber);
            $client->setAdresse($faker->address);

            $manager->persist($client);
        }

        $manager->flush();
    }
}
