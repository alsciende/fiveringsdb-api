<?php

declare(strict_types=1);

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Description of BaseApiControllerTest
 *
 * @author Cédric Bertolini <cedric.bertolini@proximedia.fr>
 */
abstract class BaseApiControllerTest extends WebTestCase
{
    public function getAnonymousClient (): Client
    {
        return static::createClient();
    }

    public function getAuthenticatedClient ($username = 'user', $password = 'user'): Client
    {
        return static::createClient(array(), array(
                    'HTTP_X-Access-Token' => $username."-access-token",
        ));
    }

    public function getContent (Client $client)
    {
        $content = json_decode($client->getResponse()->getContent(), true);
        if($content['success'] === false) {
          dump($content);
        }
        $this->assertTrue(
                $content['success']
        );
        return $content;
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
    }
}
