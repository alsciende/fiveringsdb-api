<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Ruling;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Description of RulingController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class RulingController extends AbstractApiController
{
    /**
     * Get all rulings
     * @Route("/rulings", name="listRulings")
     * @Method("GET")
     */
    public function listAction (EntityManagerInterface $entityManager)
    {
        $rulings = $entityManager
            ->getRepository(Ruling::class)
            ->findAll();

        return $this->success($rulings, [
            'Default',
            'Card',
            'card' => [
                'Id',
            ],
        ]);
    }
}