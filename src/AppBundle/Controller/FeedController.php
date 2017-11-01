<?php
/**
 * Created by PhpStorm.
 * User: cedric
 * Date: 31/10/17
 * Time: 16:53
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 */
class FeedController extends AbstractController
{
    /**
     * Get Feed
     * @Route("/feed")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function listAction ()
    {
        return $this->success(
            $this->get('app.activity_manager')->getActivity($this->getUser()),
            [
                'Default',
                'deck' => [
                    'Public',
                    'User',
                ],
            ]
        );
    }
}
