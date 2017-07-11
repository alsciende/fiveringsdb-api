<?php

declare(strict_types=1);

namespace Tests\AppBundle\Controller\API\v1;

use AppBundle\Controller\API\v1\DeckValidatorController;
use AppBundle\Service\DeckValidator;
use Tests\AppBundle\Controller\API\BaseApiControllerTest;
use Tests\AppBundle\DeckDataTrait;

class DeckValidatorControllerTest extends BaseApiControllerTest
{
    use DeckDataTrait;

    /**
     * @covers DeckValidatorController::validateAction()
     */
    public function testDeckValidatorControllerValidateAction ()
    {
        $client = $this->getAnonymousClient();
        $this->sendJsonRequest(
            $client,
            'POST',
            "/api/v1/deck-validation",
            $this->getDeckData()['cards']
        );
        $response = $client->getResponse();
        $this->assertEquals(
            200,
            $response->getStatusCode()
        );
        $content = $this->getContent($client);
        $this->assertEquals(
            DeckValidator::TOO_FEW_DYNASTY,
            $content['status']
        );
    }
}
