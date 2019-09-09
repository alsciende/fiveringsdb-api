<?php

namespace AppBundle\Controller;


use AppBundle\Entity\ClanRole;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of ClanRoleController
 *
 * @Route("/clan-roles", name="clan_roles_")
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class ClanRoleController extends AbstractApiController
{
    /**
     * Get all pairs Clan-Role
     * @Route("", name="list")
     * @Method("GET")
     */
    public function listAction (Request $request, EntityManagerInterface $entityManager)
    {
        $this->setPublic($request);

        return $this->success(
            $entityManager
                ->getRepository(ClanRole::class)
                ->findAll(),
            [
                'Default',
                'primaryRole' => [
                    'Id',
                ],
                'secondaryRole' => [
                    'Id',
                ],
            ]
        );
    }

}
