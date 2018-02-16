<?php

namespace AppBundle\Security;

use AppBundle\Entity\Token;
use AppBundle\Service\Metagame;
use AppBundle\Service\TokenManager;
use AppBundle\Service\UserManager;
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

    /** @var TokenManager $tokenManager */
    private $tokenManager;

    /** @var UserManager $userManager */
    private $userManager;

    function __construct(TokenManager $tokenManager, UserManager $userManager)
    {
        $this->tokenManager = $tokenManager;
        $this->userManager = $userManager;
    }

    public function supports(Request $request)
    {
        return $request->headers->has(self::HEADER);
    }

    public function getCredentials(Request $request)
    {
        return array_combine(
            ['tokenType', 'accessToken'],
            explode(' ', $request->headers->get(self::HEADER), 2)
        );
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = $this->tokenManager->findTokenBy($credentials);

        if (!$token instanceof Token || $token->getExpiresAt() < new \DateTime()) {
            $token = $this->tokenManager->getTokenFromProvider($credentials);
            $token->setUser($this->userManager->findTokenUser($token));
            $this->tokenManager->updateToken($token);
        }

        return $token->getUser();
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
