<?php

namespace AppBundle\Controller;

use AppBundle\Entity\PackCard;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of PackCardController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class PackCardController extends AbstractApiController
{
    /**
     * Get all pairs Pack-Card
     * @Route("/pack-cards", name="pack_cards_list")
     * @Method("GET")
     */
    public function listAction (Request $request, EntityManagerInterface $entityManager)
    {
        $this->setPublic($request);

        return $this->success(
            $entityManager
                ->getRepository(PackCard::class)
                ->findAll(),
            [
                'Default',
                'Card',
                'card' => [
                    'Id',
                ],
                'Pack',
                'pack' => [
                    'Id',
                ],
            ]
        );
    }
}
