<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 */
class UserController extends AbstractApiController
{
    /**
     * Get details about the current user
     *
     * @Route("/users/me")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function getAction ()
    {
        $user = $this->getUser();

        return $this->success(
            $user,
            [
                'Default',
                'Self',
            ]
        );
    }
}
