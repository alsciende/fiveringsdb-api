<?php

namespace Alsciende\SecurityBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;

class OauthFormLoginAuthenticator extends AbstractFormLoginAuthenticator
{

    /** @var UserPasswordEncoder */
    private $passwordEncoder;

    /** @var Router */
    private $router;

    public function __construct (UserPasswordEncoder $passwordEncoder, Router $router, \Symfony\Bridge\Monolog\Logger $logger)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->router = $router;
        $this->logger = $logger;
    }

    public function getCredentials (Request $request)
    {
        $this->logger->debug("getCredentials");
        if($request->getPathInfo() != '/oauth/v2/login_check') {
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

    protected function getLoginUrl ()
    {
        return $this->router->generate('oauth_login');
    }

    protected function getDefaultSuccessRedirectUrl ()
    {
        return $this->router->generate('oauth_auth');
    }

}
