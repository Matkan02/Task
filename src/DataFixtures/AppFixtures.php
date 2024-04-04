<?php

namespace App\DataFixtures;

use App\Entity\Tasks;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $tasks = new Tasks();
        $tasks->setDescription('Testowy opis');
        $tasks->setTitle('tytuł');

        $tasks1 = new Tasks();
        $tasks1->setDescription('Testowy opis1');
        $tasks1->setTitle('tytuł1');

        $manager->persist($tasks);
        $manager->persist($tasks1);
        $manager->flush();
    }
}
