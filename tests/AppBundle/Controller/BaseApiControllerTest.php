<?php

declare(strict_types=1);

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Description of BaseApiControllerTest
 *
 * @author Alsciende <alsciende@icloud.com>
 */
abstract class BaseApiControllerTest extends WebTestCase
{
  public function getClient(string $username = null): Client
  {
    $headers = [];
    if($username !== null) {
      $headers['HTTP_Authorization'] = "Bearer $username";
    }
    return static::createClient([], $headers);
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

    public function sendJsonRequest(Client $client, string $method, string $uri, array $data = [])
    {
        $client->request($method, $uri, [], [], [], json_encode($data));
    }

    public function assertStandardGetMany (Client $client)
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

    public function assertStandardGetOne (Client $client)
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

    public function assertStandardGetNone (Client $client)
    {
        $this->assertEquals(
                \Symfony\Component\HttpFoundation\Response::HTTP_OK, $client->getResponse()->getStatusCode()
        );
        $content = $this->getContent($client);
    }

    public function assertStatusCode(Client $client, int $statusCode)
    {
      $this->assertEquals(
        $statusCode,
        $client->getResponse()->getStatusCode()
      );
    }

    public function assertUnsuccessful(Client $client)
    {
        $this->assertEquals(
            \Symfony\Component\HttpFoundation\Response::HTTP_OK, $client->getResponse()->getStatusCode()
        );
        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertFalse(
            $content['success']
        );
    }
}
