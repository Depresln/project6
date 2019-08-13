<?php

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Comment;

class TrickTest extends KernelTestCase
{
    public function testThatWeCanGetTrick()
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $trick = $this->entityManager
            ->getRepository(Trick::class)
            ->findOneBy(array('id' => 12));

        $this->assertNotEquals($trick, NULL);
    }

    public function testThatWeCanGetUsername()
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(array('username' => 'Exal'));

        $this->assertNotEquals($user, NULL);

        $comments = $this->entityManager
            ->getRepository(Comment::class)
            ->findBy(array('user' => $user));

        $this->assertEquals(count($comments), 7);
    }
}
