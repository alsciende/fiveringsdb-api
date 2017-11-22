<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Activity;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Description of ActivityController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class ActivityController extends AbstractController
{
    /**
     * Get activity
     * @Route("/activity")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function listAction (EntityManagerInterface $entityManager)
    {
        return $this->success(
            $entityManager
                ->getRepository(Activity::class)
                ->findForUser($this->getUser(), 10),
            [
                'Default',
                'deck' => [
                    'Public'
                ],
            ]
        );
    }
}