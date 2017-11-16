<?php
/**
 * Created by PhpStorm.
 * User: cedric
 * Date: 31/10/17
 * Time: 16:53
 */

namespace AppBundle\Controller;

use AppBundle\Service\ActivityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 */
class FeedController extends AbstractController
{
    /**
     * Get Feed
     * @Route("/feed")
     * @Method("GET")
     */
    public function listAction ()
    {
        return $this->success(
            $this->get(ActivityManager::class)->getActivity($this->getUser()),
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
