<?php

declare(strict_types=1);

namespace Tests\AppBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Client;

/**
 * Description of BaseApiControllerTest
 *
 * @author Cédric Bertolini <cedric.bertolini@proximedia.fr>
 */
abstract class BaseApiControllerTest extends \Tests\AppBundle\Controller\BaseControllerTest
{

    public function getContent (Client $client)
    {
        return json_decode($client->getResponse()->getContent(), true);
    }

    /**
     * 
     * @return Client
     */
    public function getAuthenticatedClient ($username = 'user', $password = 'user')
    {
        return static::createClient(array(), array(
                    'HTTP_X-Access-Token' => $username."-access-token",
        ));
    }
    
    public function sendJsonRequest(\Symfony\Component\BrowserKit\Client $client, string $method, string $uri, array $data = [])
    {
        $client->request($method, $uri, [], [], [], json_encode($data));
    }

    public function assertStandardGetMany (\Symfony\Bundle\FrameworkBundle\Client $client)
    {
        $this->assertEquals(
                \Symfony\Component\HttpFoundation\Response::HTTP_OK, $client->getResponse()->getStatusCode()
        );
        $content = $this->getContent($client);
        $this->assertTrue(
                $content['success']
        );
        $this->assertGreaterThan(
                0, $content['size']
        );
        $this->assertEquals(
                $content['size'], count($content['records'])
        );
        return $content['records'];
    }

    public function assertStandardGetOne (\Symfony\Bundle\FrameworkBundle\Client $client)
    {
        $this->assertEquals(
                \Symfony\Component\HttpFoundation\Response::HTTP_OK, $client->getResponse()->getStatusCode()
        );
        $content = $this->getContent($client);
        $this->assertTrue(
                $content['success']
        );
        $this->assertArrayHasKey(
                'record', $content
        );
        return $content['record'];
    }

    public function assertStandardGetNone (\Symfony\Bundle\FrameworkBundle\Client $client)
    {
        $this->assertEquals(
                \Symfony\Component\HttpFoundation\Response::HTTP_OK, $client->getResponse()->getStatusCode()
        );
        $content = $this->getContent($client);
        $this->assertTrue(
                $content['success']
        );
    }
}
