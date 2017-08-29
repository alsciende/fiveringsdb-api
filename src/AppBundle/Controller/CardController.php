<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Card;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Description of CardsController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class CardController extends BaseApiController
{
    /**
     * Get all Cards
     * @Route("/cards", name="listCards")
     * @Method("GET")
     */
    public function listAction ()
    {
        return $this->success(
            $this
                ->getDoctrine()
                ->getRepository(Card::class)
                ->findAll(),
            [
                'Default',
                'packs_group',
                'packCards' => [
                    'Default',
                    'pack_group',
                    'pack' => [
                        'id_group',
                    ],
                ],
            ]
        );
    }

    /**
     * Get a Card
     * @Route("/cards/{id}", name="getCard")
     * @Method("GET")
     */
    public function getAction (Card $card)
    {
        return $this->success($card);
    }
}
