<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Trick;
use App\Entity\Category;
use App\Entity\Comment;

class TrickFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');

        // Create 3 categories
        for($i = 1; $i <= 3; $i++){
            $category = new Category();
            $category->setTitle($faker->sentence());

            $manager->persist($category);
        }

        // Create between 4 and 6 tricks with comments
        for($j = 1; $j <= mt_rand(4, 6); $j++){
            $trick = new Trick();

            $content = '<p>' . join($faker->paragraphs(5), '</p><p>') . '</p>';

            $trick->setTitle($faker->sentence())
                  ->setDescription($content)
                  ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                  ->setCategory($category);

            $manager->persist($trick);

            for($k = 1; $k <= mt_rand(4,10); $k++){
                $comment = new Comment();

                $content = '<p>' . join($faker->paragraphs(2), '</p><p>') . '</p>';

                $days = (new \DateTime())->diff($trick->getCreatedAt())->days;

                $comment->setContent($content)
                        ->setCreatedAt($faker->dateTimeBetween('-' . $days . ' days'))
                        ->setTrick($trick);

                $manager->persist($comment);
            }
        }

        $manager->flush();
    }
}
