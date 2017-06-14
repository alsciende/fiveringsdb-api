<?php

namespace AppBundle\Controller\API\v1;

use AppBundle\Controller\API\BaseApiController;
use AppBundle\Entity\Type;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Description of TypesController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class TypeController extends BaseApiController
{

    /**
     * Get all Types
     *
     * @ApiDoc(
     *  resource=true,
     *  output="AppBundle\Entity\Type",
     *  section="Cards",
     * )
     * @Route("/types")
     * @Method("GET")
     */
    public function listAction ()
    {
        $Types = $this->getDoctrine()->getRepository(Type::class)->findAll();
        return $this->success($Types);
    }

}
