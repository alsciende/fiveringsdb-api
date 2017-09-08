<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Pack;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

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
    public function listAction (Request $request)
    {
        $this->setPublic($request);

        return $this->success(
            $this
                ->getDoctrine()
                ->getRepository(Pack::class)
                ->findAll(),
            [
                'Default',
                'cycle_group',
                'cycle' => [
                    'id_group',
                ],
            ]
        );
    }
}
