<?php

namespace AppBundle\Service;

use AppBundle\Entity\Token;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Service to communicate with the Metagame server
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Metagame
{
    /** @var array */
    private $parameters;

    /** @var string */
    private $logsDir;

    /** @var string */
    private $environment;

    /** @var boolean */
    private $debug;

    public function __construct($parameters, $logsDir, $environment, $debug)
    {
        $this->parameters = $parameters;
        $this->logsDir = $logsDir;
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

    private function getClient(Token $token = null): Client
    {
        $options = [
            'base_uri' => $this->parameters['base_uri'],
        ];

        if ($this->debug) {
            $options['handler'] = $this->getDebugStack();
        }

        if ($token instanceof Token) {
            $options['headers']['Authorization'] = $token->toHeader();
        }

        return new Client($options);
    }

    /**
     * @param string $code
     * @return array
     */
    public function getTokenData(string $code): array
    {
        $response = $this->get(
            'oauth/v2/token', [
                'client_id'     => $this->parameters['client_id'],
                'client_secret' => $this->parameters['client_secret'],
                'redirect_uri'  => $this->parameters['redirect_uri'],
                'grant_type'    => 'authorization_code',
                'code'          => $code,
            ]
        );

        if ($response->getStatusCode() !== 200) {
            throw new AccessDeniedException($response->getReasonPhrase());
        }

        $jsonDecode = json_decode((string) $response->getBody(), true);

        if (is_array($jsonDecode)) {
            return $jsonDecode;
        }

        throw new \LogicException('Token data response did not decode to an array: ' . $response->getBody());
    }

    /**
     * @param Token $token
     * @return array
     */
    public function getUserData(Token $token): array
    {
        $response = $this->get('api/users/me', [], $token);

        if ($response->getStatusCode() !== 200) {
            throw new AccessDeniedException((string) $response->getBody());
        }

        $jsonDecode = json_decode((string) $response->getBody(), true);

        if (is_array($jsonDecode)) {
            return $jsonDecode;
        }

        throw new \LogicException('User data response did not decode to an array: ' . $response->getBody());
    }

    public function get(string $url, array $parameters = [], Token $token = null): Response
    {
        return $this->getClient($token)->request(
            'GET', $url, [
                'query' => $parameters,
            ]
        );
    }

    public function post(string $url, array $parameters = [], Token $token = null): Response
    {
        return $this->getClient($token)->request(
            'POST', $url, [
                'json' => $parameters,
            ]
        );
    }
}