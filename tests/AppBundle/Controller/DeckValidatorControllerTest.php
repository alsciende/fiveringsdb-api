<?php

declare(strict_types=1);

namespace Tests\AppBundle\Controller;

use AppBundle\Controller\DeckValidatorController;
use AppBundle\Service\DeckValidator;
use Tests\AppBundle\Controller\BaseApiControllerTest;
use Tests\AppBundle\DeckDataTrait;

class DeckValidatorControllerTest extends BaseApiControllerTest
{
    use DeckDataTrait;

    /**
     * @covers DeckValidatorController::validateAction()
     */
    public function testDeckValidatorControllerValidateAction ()
    {
        $client = $this->getClient();
        $this->sendJsonRequest(
            $client,
            'POST',
            "/deck-validation",
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
