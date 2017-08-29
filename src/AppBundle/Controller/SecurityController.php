<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Description of SecurityController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class SecurityController extends \Symfony\Bundle\FrameworkBundle\Controller\Controller
{
    /**
     * @Route("/login", name="security_login")
     */
    public function loginAction ()
    {
        $helper = $this->get('security.authentication_utils');

        return $this->render(
            'security/login.html.twig', [
            // last username entered by the user (if any)
            'last_username' => $helper->getLastUsername(),
            // last authentication error (if any)
            'error'         => $helper->getLastAuthenticationError(),
        ]
        );
    }

    /**
     * @Route("/login_check", name="security_login_check")
     */
    public function loginCheckAction ()
    {
        throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException("This controller shouldn't be called");
    }
}
