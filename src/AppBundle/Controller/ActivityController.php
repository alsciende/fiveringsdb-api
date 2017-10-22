<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Activity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Description of ActivityController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class ActivityController extends BaseApiController
{
    /**
     * Get activity
     * @Route("/activity")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function listAction ()
    {
        return $this->success(
            $this
                ->getDoctrine()
                ->getRepository(Activity::class)
                ->findBy(['user' => $this->getUser()], ['createdAt' => 'DESC'], 10),
            [
                'Default',
            ]
        );
    }
}