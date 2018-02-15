<?php

namespace AppBundle\Security;

use AppBundle\Entity\Token;
use AppBundle\Service\TokenManager;
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

    function __construct(TokenManager $tokenManager)
    {
        $this->tokenManager = $tokenManager;
    }

    /**
     * Does the authenticator support the given Request?
     */
    public function supports(Request $request)
    {
        return $request->headers->has(self::HEADER);
    }

    /**
     * Called on every request. Return whatever credentials you want,
     * or null to stop authentication.
     */
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
        if ($token instanceof Token && $token->getExpiresAt() > new \DateTime()) {
            return $token->getUser();
        }

        return null;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        // check credentials - e.g. make sure the password is valid
        // no credential check is needed in this case
        // return true to cause authentication success
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    /**
     * Called when authentication is needed, but it's not sent
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = [
            // you might translate this message
            'message' => 'Authentication Required',
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
