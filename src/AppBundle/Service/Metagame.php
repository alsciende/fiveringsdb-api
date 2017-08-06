<?php

namespace AppBundle\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Service to communicate with the Metagame server
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Metagame
{
    /** @var string */
    private $baseUri;

    /** @var string */
    private $logsDir;

    /** @var string */
    private $environment;

    /** @var boolean */
    private $debug;

    public function __construct ($baseUri, $logsDir, $environment, $debug)
    {
        $this->logsDir = $logsDir;
        $this->baseUri = $baseUri;
        $this->environment = $environment;
        $this->debug = $debug;
    }

    private function getDebugStack(): HandlerStack
    {
        $logger = new Logger('guzzle');
        $logger->pushHandler(
            new StreamHandler(
                $this->logsDir . '/' . $this->environment . '.log',
                Logger::DEBUG
            )
        );

        $stack = HandlerStack::create();
        $stack->push(Middleware::log($logger, new MessageFormatter('{response}')));
        $stack->push(Middleware::log($logger, new MessageFormatter('{request}')));
        return $stack;
    }

    private function getClient (string $token = null): Client
    {
        $options = [
            'base_uri' => $this->baseUri,
        ];

        if($this->debug) {
            $options['handler'] = $this->getDebugStack();
        }

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