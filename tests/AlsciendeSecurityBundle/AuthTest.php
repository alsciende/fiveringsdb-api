<?php

namespace Tests\AlsciendeSecurityBundle;

/**
 * Description of AuthTest
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class AuthTest extends \Symfony\Bundle\FrameworkBundle\Test\WebTestCase
{

    public function testGetIndex ()
    {
        $client = static::createClient();

        $client->request('GET', '/');
        $this->assertEquals(
                200, $client->getResponse()->getStatusCode()
        );
    }

    public function testGetProfileFailure ()
    {
        $client = static::createClient();

        $client->request('GET', '/profile');
        $this->assertEquals(
                302, $client->getResponse()->getStatusCode()
        );
    }

    public function testGetProfileSuccess ()
    {
        $client = static::createClient(array(), array(
                    'PHP_AUTH_USER' => 'admin',
                    'PHP_AUTH_PW' => 'test',
        ));

        $client->request('GET', '/profile');
        $this->assertEquals(
                200, $client->getResponse()->getStatusCode()
        );
    }

}
