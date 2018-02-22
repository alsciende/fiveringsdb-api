<?php

namespace AppBundle\Security;

use AppBundle\Service\UserManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
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

    /** @var OauthCredentialsManager $credentialsManager */
    private $credentialsManager;

    /** @var UserManager $userManager */
    private $userManager;

    /** @var LoggerInterface $logger */
    private $logger;

    function __construct(OauthCredentialsManager $credentialsManager, UserManager $userManager, LoggerInterface $securityLogger)
    {
        $this->credentialsManager = $credentialsManager;
        $this->userManager = $userManager;
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
        $userId = $this->credentialsManager->getUserId($credentials);

        if ($userId === null) {
            $this->logger->info('Invalid token.', ['credentials' => $credentials]);

            return null;
        }

        try {
            $user = $userProvider->loadUserByUsername($userId);
        } catch (UsernameNotFoundException $exception) {
            $data = $this->credentialsManager->getUserData($credentials);
            if ($data === null) {
                return null;
            }

            $user = $this->userManager->createUser($data['id'], $data['username']);
            $this->userManager->updateUser($user);
            $this->logger->info('User created from data.', $data);
        }

        return $user;
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
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData()),
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
