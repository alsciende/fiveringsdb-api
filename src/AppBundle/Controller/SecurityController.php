<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Description of SecurityController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class SecurityController extends Controller
{
    /**
     * @Route("/login", name="security_login")
     */
    public function loginAction (AuthenticationUtils $authenticationUtils)
    {
        return $this->render(
            'security/login.html.twig', [
                // last username entered by the user (if any)
                'last_username' => $authenticationUtils->getLastUsername(),
                // last authentication error (if any)
                'error'         => $authenticationUtils->getLastAuthenticationError(),
            ]
        );
    }

    /**
     * @Route("/login_check", name="security_login_check")
     */
    public function loginCheckAction ()
    {
        throw new BadRequestHttpException("This controller shouldn't be called");
    }
}
