<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Trick;

class TrickFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for($i = 1; $i <= 10; $i++){
            $trick = new Trick();
            $trick->setTitle("Titre du trick n°$i")
                  ->setDescription("<p>Contenu du trick n°$i</p>")
                  ->setCreatedAt(new \DateTime());

            $manager->persist($trick);
        }

        $manager->flush();
    }
}
