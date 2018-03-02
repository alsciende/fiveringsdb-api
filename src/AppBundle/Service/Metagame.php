<?php

namespace AppBundle\Service;

use AppBundle\Behavior\Service\OauthServiceInterface;
use AppBundle\Security\Token;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

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
class Metagame implements OauthServiceInterface
{
    /** @var string $baseUri */
    private $baseUri;

    /** @var string */
    private $logsDir;

    /** @var string */
    private $environment;

    /** @var boolean */
    private $debug;

    public function __construct(string $baseUri, string $logsDir, string $environment, bool $debug)
    {
        $this->baseUri = $baseUri;
        $this->logsDir = $logsDir;
        $this->environment = $environment;
        $this->debug = $debug;
    }

    private function get(string $url, array $parameters = [], string $credentials = null): Response
    {
        return $this->getClient($credentials)->request(
            'GET', $url, [
                'query' => $parameters,
            ]
        );
    }

    private function getClient(string $credentials = null): Client
    {
        $options = [
            'base_uri' => $this->baseUri,
        ];

        if ($this->debug) {
            $options['handler'] = $this->getDebugStack();
        }

        if ($credentials !== null) {
            $options['headers']['Authorization'] = $credentials;
        }

        return new Client($options);
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

    /**
     * @param string $credentials
     * @return null|string
     */
    public function getUserData(string $credentials): ?string
    {
        try {
            $response = $this->get('api/users/me', [], $credentials);
        } catch(ClientException $e) {
            return null;
        }

        return (string) $response->getBody();
    }

    private function post(string $url, array $parameters = [], string $credentials = null): Response
    {
        return $this->getClient($credentials)->request(
            'POST', $url, [
                'json' => $parameters,
            ]
        );
    }
}