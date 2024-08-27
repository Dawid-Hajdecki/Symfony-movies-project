<?php

namespace App\DataFixtures;

use App\Entity\Actor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ActorFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $actor = new Actor();
        $actor->setName('Dawid Hajdecki1');
        $manager->persist($actor);

        $actor2 = new Actor();
        $actor2->setName('Dawid Hajdecki2');
        $manager->persist($actor2);

        $actor3 = new Actor();
        $actor3->setName('Dawid Hajdecki3');
        $manager->persist($actor3);

        $actor4 = new Actor();
        $actor4->setName('Dawid Hajdecki4');
        $manager->persist($actor4);

        $manager->flush();

        $this->addReference('actor1', $actor);
        $this->addReference('actor2', $actor2);
        $this->addReference('actor3', $actor3);
        $this->addReference('actor4', $actor4);
    }
}
