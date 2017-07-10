<?php

namespace AppBundle\Controller\API\v1;

use AppBundle\Controller\API\BaseApiController;
use AppBundle\Entity\Deck;
use AppBundle\Service\DeckValidator;
use JMS\Serializer\Serializer;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
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
     * @ApiDoc(
     *  resource=true,
     *  section="Utils",
     * )
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
