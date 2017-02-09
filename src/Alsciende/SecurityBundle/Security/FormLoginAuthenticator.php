<?php

namespace Alsciende\SecurityBundle\Security;

use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class FormLoginAuthenticator extends AbstractGuardAuthenticator
{

    use TargetPathTrait;

    /** @var UserPasswordEncoder */
    private $passwordEncoder;

    /** @var Router */
    private $router;

    public function __construct (UserPasswordEncoder $passwordEncoder, Router $router, Logger $logger)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->router = $router;
        $this->logger = $logger;
    }

    public function getCredentials (Request $request)
    {
        $this->logger->debug("getCredentials");
        if($request->getPathInfo() != '/login_check') {
            return null;
        }
        $username = $request->request->get('_username');
        $request->getSession()->set(Security::LAST_USERNAME, $username);
        $password = $request->request->get('_password');
        return array(
            'username' => $username,
            'password' => $password
        );
    }

    public function getUser ($credentials, UserProviderInterface $userProvider)
    {
        $this->logger->debug("getUser");
        $username = $credentials['username'];
        return $userProvider->loadUserByUsername($username);
    }

    public function checkCredentials ($credentials, UserInterface $user)
    {
        $this->logger->debug("checkCredentials");
        return true;
        /*
          $plainPassword = $credentials['password'];
          if(!$this->encoder->isPasswordValid($user, $plainPassword)) {
          throw new BadCredentialsException();
          }
         */
    }

    public function onAuthenticationSuccess (Request $request, TokenInterface $token, $providerKey)
    {
        $targetPath = null;

        $this->logger->debug("onAuthenticationSuccess");
        $this->logger->debug($request->hasSession() ? "has session" : "doesn't have session");

        // if the user hit a secure page and start() was called, this was
        // the URL they were on, and probably where you want to redirect to
        if($request->getSession() instanceof SessionInterface) {
            $targetPath = $this->getTargetPath($request->getSession(), $providerKey);
        }

        if(!$targetPath) {
            $targetPath = $this->router->generate('app_default_index');
        }

        return new RedirectResponse($targetPath);
    }

    public function onAuthenticationFailure (Request $request, AuthenticationException $exception)
    {
        $this->logger->debug("onAuthenticationFailure");
        $this->logger->debug($request->hasSession() ? "has session" : "doesn't have session");

        if($request->getSession() instanceof SessionInterface) {
            $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        }

        $url = $this->router->generate('security_login');

        return new RedirectResponse($url);
    }

    public function supportsRememberMe ()
    {
        return true;
    }

    /**
     * Called when authentication is needed, but it's not sent
     */
    public function start (Request $request, AuthenticationException $authException = null)
    {
        $url = $this->router->generate('security_login');

        return new RedirectResponse($url);
    }

}
