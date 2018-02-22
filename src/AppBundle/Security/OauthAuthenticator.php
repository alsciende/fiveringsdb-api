<?php

namespace AppBundle\Security;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

/**
 * Description of OauthAuthenticator
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class OauthAuthenticator extends AbstractGuardAuthenticator
{
    const HEADER = 'Authorization';

    /** @var OauthCredentialsManager $manager */
    private $manager;

    /** @var LoggerInterface $logger */
    private $logger;

    function __construct(OauthCredentialsManager $manager, LoggerInterface $securityLogger)
    {
        $this->manager = $manager;
        $this->logger = $securityLogger;
    }

    public function supports(Request $request)
    {
        return $request->headers->has(self::HEADER);
    }

    public function getCredentials(Request $request)
    {
        return $request->headers->get(self::HEADER);
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $userId = $this->manager->getUserId($credentials);

        if($userId === null) {
            $this->logger->notice('User not found.', ['credentials' => $credentials]);

            return null;
        }

        return $userProvider->loadUserByUsername($userId);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = [
            'message' => 'Authentication Required',
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
