<?php

namespace AppBundle\Controller;

use AppBundle\Behavior\Service\GetRepositoryTrait;
use AppBundle\Entity\Activity;
use AppBundle\Repository\ActivityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Description of ActivityController
 *
 * @Route("/activity", name="activity_")
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class ActivityController extends AbstractApiController
{
    use GetRepositoryTrait;

    /**
     * Get activity
     *
     * @Route("", name="list")
     * @Method("GET")
     *
     * @Security("has_role('ROLE_USER')")
     */
    public function listAction (EntityManagerInterface $entityManager)
    {
        /** @var ActivityRepository $repository */
        $repository = $this->getRepository($entityManager, Activity::class);

        return $this->success(
            $repository
                ->findForUser($this->getUser(), 10),
            [
                'Default',
                'deck' => [
                    'Public',
                ],
            ]
        );
    }
}