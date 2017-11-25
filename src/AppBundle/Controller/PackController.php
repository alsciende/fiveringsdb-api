<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Pack;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of PacksController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class PackController extends AbstractApiController
{
    /**
     * Get all Packs
     * @Route("/packs")
     * @Method("GET")
     */
    public function listAction (Request $request, EntityManagerInterface $entityManager)
    {
        $this->setPublic($request);

        return $this->success(
            $entityManager
                ->getRepository(Pack::class)
                ->findAllSorted(),
            [
                'Default',
                'Cycle',
                'cycle' => [
                    'Id',
                ],
            ]
        );
    }
}
