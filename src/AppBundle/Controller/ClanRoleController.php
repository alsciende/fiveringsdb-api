<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ClanRole;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of ClanRoleController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class ClanRoleController extends BaseApiController
{
    /**
     * Get all pairs Clan-Role
     * @Route("/clan-roles")
     * @Method("GET")
     */
    public function listAction (Request $request)
    {
        $this->setPublic($request);

        return $this->success(
            $this
                ->getDoctrine()
                ->getRepository(ClanRole::class)
                ->findAll(),
            [
                'Default',
                'Card',
                'card' => [
                    'Id',
                ],
            ]
        );
    }

}
