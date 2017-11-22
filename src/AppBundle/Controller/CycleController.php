<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Cycle;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of CyclesController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class CycleController extends AbstractController
{
    /**
     * Get all Cycles
     * @Route("/cycles")
     * @Method("GET")
     */
    public function listAction (Request $request)
    {
        $this->setPublic($request);

        return $this->success(
            $this
                ->getDoctrine()
                ->getRepository(Cycle::class)
                ->findAll(),
            [
                'Default',
            ]
        );
    }
}
