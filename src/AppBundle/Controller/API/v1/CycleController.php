<?php

namespace AppBundle\Controller\API\v1;

use AppBundle\Controller\API\BaseApiController;
use AppBundle\Entity\Cycle;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Description of CyclesController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class CycleController extends BaseApiController
{

    /**
     * Get all Cycles
     * 
     * @ApiDoc(
     *  resource=true,
     *  output="AppBundle\Entity\Cycle",
     *  section="Cards",
     * )
     * @Route("/cycles")
     * @Method("GET")
     */
    public function listAction ()
    {
        $cycles = $this->getDoctrine()->getRepository(Cycle::class)->findAll();
        return $this->success($cycles);
    }

}
