<?php

namespace App\DataFixtures;

use App\Entity\Movie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MovieFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $movie = new Movie();
        $movie->setTitle('The Great Movie');
        $movie->setReleaseYear('2010');
        $movie->setDescription('The Great Movie description');
        $movie->setImagePath('https://cdn.pixabay.com/photo/2024/08/05/13/17/dog-8946829_1280.jpg');
        $movie->addActor($this->getReference('actor1'));
        $movie->addActor($this->getReference('actor4'));

        $manager->persist($movie);

        $movie2 = new Movie();
        $movie2->setTitle('Avengers');
        $movie2->setReleaseYear('2015');
        $movie2->setDescription('Avengers description');
        $movie2->setImagePath('https://cdn.pixabay.com/photo/2024/01/15/21/13/puppy-8510899_1280.jpg');
        $movie2->addActor($this->getReference('actor2'));
        $movie2->addActor($this->getReference('actor3'));

        $manager->persist($movie2);

        $manager->flush();
    }
}
