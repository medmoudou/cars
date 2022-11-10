<?php

namespace App\DataFixtures;

use App\Entity\Car;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = (new \Faker\Factory())::create();
        $faker->addProvider(new \Faker\Provider\Fakecar($faker));

        $category = array();
        for ($i = 0; $i < 10; $i++) {
            $category[$i] = new Category();
            $category[$i]->setName($faker->vehicleType);
            $manager->persist($category[$i]);
            $this->addReference("CATEGORY" . $i, $category[$i]);
        }

        $car = array();
        for ($i = 0; $i < 100; $i++) {
            $car[$i] = new Car();
            $car[$i]->setName($faker->vehicle);
            $car[$i]->setNbDoors($faker->vehicleDoorCount);
            $car[$i]->setNbSeats($faker->vehicleSeatCount);
            $car[$i]->setCost(floatVal(rand(10000, 30000) . '.' . rand(00, 99)));
            $car[$i]->setCategory($this->getReference("CATEGORY" . mt_rand(0, 9)));
            $manager->persist($car[$i]);
        }

        $manager->flush();
    }
}
