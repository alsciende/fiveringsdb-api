<?php

namespace AppBundle\Controller\API\v1;

use AppBundle\Manager\DeckManager;
use AppBundle\Model\CardSlotCollectionDecorator;
use AppBundle\Service\DeckValidator;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of DeckValidatorController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DeckValidatorController extends Controller
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
        $data = json_decode($request->getContent(), TRUE);

        /** @var $manager DeckManager */
        $manager = $this->get('app.deck_manager');

        $deckCards = $manager->denormalizeDeckCards($data);

        /** @var $validator DeckValidator */
        $validator = $this->get('app.deck_validator');

        return new JsonResponse([
            'status' => $validator->check(new CardSlotCollectionDecorator($deckCards))
        ]);
    }
}