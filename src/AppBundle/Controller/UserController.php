<?php
/**
 * Created by PhpStorm.
 * User: cedric
 * Date: 20/10/17
 * Time: 10:05
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 */
class UserController extends AbstractController
{
    /**
     * Get details about the current user
     *
     * @Route("/users/me")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function getAction (Request $request)
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
