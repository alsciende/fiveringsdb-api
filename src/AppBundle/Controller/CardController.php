<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Card;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of CardsController
 *
 * @Route("/cards", name="cards_")
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class CardController extends AbstractApiController
{
    /**
     * Get all Cards
     * @Route("", name="list")
     * @Method("GET")
     */
    public function listAction (Request $request, EntityManagerInterface $entityManager)
    {
        $this->setPublic($request);

        return $this->success(
            $entityManager
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
     * @Route("/{id}", name="get")
     * @Method("GET")
     */
    public function getAction (Request $request, Card $card)
    {
        $this->setPublic($request);

        return $this->success($card);
    }
}
