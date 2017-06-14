<?php

namespace AppBundle\Controller\API\v1;

use AppBundle\Controller\API\BaseApiController;
use AppBundle\Entity\Element;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Description of ElementsController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class ElementController extends BaseApiController
{

    /**
     * Get all Elements
     *
     * @ApiDoc(
     *  resource=true,
     *  output="AppBundle\Entity\Element",
     *  section="Cards",
     * )
     * @Route("/elements")
     * @Method("GET")
     */
    public function listAction ()
    {
        $Elements = $this->getDoctrine()->getRepository(Element::class)->findAll();
        return $this->success($Elements);
    }

}
