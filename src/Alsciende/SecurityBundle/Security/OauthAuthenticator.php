<?php

namespace Alsciende\SecurityBundle\Security;

use FOS\OAuthServerBundle\Entity\AccessTokenManager;
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
    
    /** @var AccessTokenManager */
    private $accessTokenManager;
    
    function __construct (AccessTokenManager $accessTokenManager)
    {
        $this->accessTokenManager = $accessTokenManager;
    }

    /**
     * Called on every request. Return whatever credentials you want,
     * or null to stop authentication.
     */
    public function getCredentials (Request $request)
    {
        if(!$token = $request->query->get('access_token')) {
            // no token? Return null and no other methods will be called
            return;
        }

        // What you return here will be passed to getUser() as $credentials
        return array(
            'token' => $token,
        );
    }

    public function getUser ($credentials, UserProviderInterface $userProvider)
    {
        $token = $credentials['token'];

        $accessToken = $this->accessTokenManager->findTokenByToken($token);
        if(!$accessToken) {
            return null;
        }
        
        // if null, authentication will fail
        // if a User object, checkCredentials() is called
        return $accessToken->getUser();
    }

    public function checkCredentials ($credentials, UserInterface $user)
    {
        // check credentials - e.g. make sure the password is valid
        // no credential check is needed in this case
        // return true to cause authentication success
        return true;
    }

    public function onAuthenticationSuccess (Request $request, TokenInterface $token, $providerKey)
    {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure (Request $request, AuthenticationException $exception)
    {
        $data = array(
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

                // or to translate this message
                // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        );

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    /**
     * Called when authentication is needed, but it's not sent
     */
    public function start (Request $request, AuthenticationException $authException = null)
    {
        $data = array(
            // you might translate this message
            'message' => 'Authentication Required'
        );

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe ()
    {
        return false;
    }

}
