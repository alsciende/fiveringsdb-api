<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Card;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of CardsController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class CardController extends AbstractController
{
    /**
     * Get all Cards
     * @Route("/cards", name="listCards")
     * @Method("GET")
     */
    public function listAction (Request $request)
    {
        $this->setPublic($request);

        return $this->success(
            $this
                ->getDoctrine()
                ->getRepository(Card::class)
                ->findAll(),
            [
                'Default',
                'PackCards',
                'packCards' => [
                    'Default',
                    'Pack',
                    'pack' => [
                        'Id',
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
    public function getAction (Request $request, Card $card)
    {
        $this->setPublic($request);

        return $this->success($card);
    }
}
