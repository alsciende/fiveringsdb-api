<?php

namespace AppBundle\Controller;

use AppBundle\Controller\BaseApiController;
use AppBundle\Entity\Pack;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Description of PacksController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class PackController extends BaseApiController
{
    /**
     * Get all Packs
     * @Route("/packs")
     * @Method("GET")
     */
    public function listAction ()
    {
        return $this->success(
            $this
                ->getDoctrine()
                ->getRepository(Pack::class)
                ->findAll(),
            [
                'Default',
                'cycle_group',
                'cycle' => [
                    'code_group'
                ]
            ]
        );
    }
}
