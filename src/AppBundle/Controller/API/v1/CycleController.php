<?php

namespace AppBundle\Controller\API\v1;

use AppBundle\Controller\API\BaseApiController;
use AppBundle\Entity\Cycle;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Description of CyclesController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class CycleController extends BaseApiController
{
    /**
     * Get all Cycles
     * @Route("/cycles")
     * @Method("GET")
     */
    public function listAction ()
    {
        return $this->success(
            $this
                ->getDoctrine()
                ->getRepository(Cycle::class)
                ->findAll()
        );
    }
}
