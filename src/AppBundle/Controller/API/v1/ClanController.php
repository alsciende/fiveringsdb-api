<?php

namespace AppBundle\Controller\API\v1;

use AppBundle\Controller\API\BaseApiController;
use AppBundle\Entity\Clan;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Description of ClansController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class ClanController extends BaseApiController
{

    /**
     * Get all Clans
     *
     * @ApiDoc(
     *  resource=true,
     *  output="AppBundle\Entity\Clan",
     *  section="Cards",
     * )
     * @Route("/clans")
     * @Method("GET")
     */
    public function listAction ()
    {
        $Clans = $this->getDoctrine()->getRepository(Clan::class)->findAll();
        return $this->success($Clans);
    }

}
