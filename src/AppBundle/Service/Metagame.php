<?php

namespace AppBundle\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

/**
 * Service to communicate with the Metagame server
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Metagame
{
    private $baseURI = 'http://metagame.local:8080/app_dev.php/';

    private function getClient (string $token = null): Client
    {
        $options = [
            'base_uri' => $this->baseURI,
        ];

        if ($token !== null) {
            $options['headers']['Authorization'] = "Bearer $token";
        }

        return new Client($options);
    }

    public function get ($url, $parameters = [], $token = null): Response
    {
        return $this->getClient($token)->request('GET', $url, [
            'query' => $parameters
        ]);
    }

    public function post ($url, $parameters = [], $token = null): Response
    {
        return $this->getClient($token)->request('POST', $url, [
            'json' => $parameters
        ]);
    }
}