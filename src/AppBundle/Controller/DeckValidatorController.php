<?php

namespace AppBundle\Controller;

use AppBundle\Controller\BaseApiController;
use AppBundle\Entity\Deck;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of DeckValidatorController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DeckValidatorController extends BaseApiController
{
    /**
     * @Route("/deck-validation")
     * @Method("POST")
     */
    public function validateAction(Request $request)
    {
        /** @var Deck $deck */
        $deck = $this->get('jms_serializer')->fromArray([
            'cards' => json_decode($request->getContent(), TRUE)
        ], Deck::class);

        return new JsonResponse([
            'status' => $this->get('app.deck_validator')->check($deck->getDeckCards())
        ]);
    }
}
