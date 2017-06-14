<?php

namespace AppBundle\Controller\API\v1;

use AppBundle\Controller\API\BaseApiController;
use AppBundle\Entity\Pack;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Description of PacksController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class PackController extends BaseApiController
{

    /**
     * Get all Packs
     * 
     * @ApiDoc(
     *  resource=true,
     *  output="AppBundle\Entity\Pack",
     *  section="Cards",
     * )
     * @Route("/packs")
     * @Method("GET")
     */
    public function listAction ()
    {
        $packs = $this->getDoctrine()->getRepository(Pack::class)->findAll();
        return $this->success($packs);
    }

}
