<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AccessibilityTest extends WebTestCase
{
    public function testRedirectIfNotLogged(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isRedirect());
    }
}
