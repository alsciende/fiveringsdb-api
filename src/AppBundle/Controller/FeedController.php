<?php

namespace AppBundle\Controller;

use AppBundle\Service\ActivityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 */
class FeedController extends AbstractApiController
{
    /**
     * Get Feed
     * @Route("/feed")
     * @Method("GET")
     */
    public function listAction (ActivityManager $activityManager)
    {
        return $this->success(
            $activityManager->getActivity($this->getUser()),
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
