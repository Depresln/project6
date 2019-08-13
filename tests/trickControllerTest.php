<?php

use PHPUnit\Framework\TestCase;
use Symfony\Component\HTTPFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class trickControllerTest extends WebTestCase
{
    public function testHomepageIsUp()
    {
        $user = static::createClient();
        $user->request('GET', '/');

        $this->assertEquals(200, $user->getResponse()->getStatusCode());
    }

    public function testUserSpaceRedirect()
    {
        $user = static::createClient();
        $user->request('GET', '/user/1');

        $this->assertEquals(302, $user->getResponse()->getStatusCode());
    }
}
