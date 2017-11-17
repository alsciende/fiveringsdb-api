<?php

namespace AppBundle\Controller;


use AppBundle\Model\CardSlotCollectionDecorator;

use AppBundle\Service\DeckValidator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of DeckValidatorController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DeckValidatorController extends AbstractController
{
    /**
     * @Route("/deck-validation/{format}")
     * @Method("POST")
     */
    public function validateAction (Request $request, string $format)
    {
        $cardSlotCollection = $this->get('jms_serializer')->fromArray(
            json_decode($request->getContent(), true)
            , CardSlotCollectionDecorator::class
        );

        return new JsonResponse(
            [
                'success' => true,
                'status'  => $this->get(DeckValidator::class)->check($cardSlotCollection, $format),
            ]
        );
    }
}
