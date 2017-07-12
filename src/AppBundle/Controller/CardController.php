<?php

namespace AppBundle\Controller;

use AppBundle\Controller\BaseApiController;
use AppBundle\Entity\Card;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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
                        'code_group'
                    ]
                ]
            ]
        );
    }
    
    /**
     * Get a Card
     * @Route("/cards/{code}", name="getCard")
     * @Method("GET")
     * @ParamConverter("card", class="AppBundle:Card", options={"id" = "code"})
     */
    public function getAction (Card $card)
    {
        return $this->success($card);
    }
}
