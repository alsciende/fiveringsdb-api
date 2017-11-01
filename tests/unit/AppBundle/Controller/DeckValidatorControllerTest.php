<?php

declare(strict_types=1);

namespace Tests\Unit\AppBundle\Controller;

use AppBundle\Controller\DeckValidatorController;
use AppBundle\Service\DeckValidator;
use Tests\Unit\AppBundle\Controller\BaseApiControllerTest;
use Tests\Unit\AppBundle\DeckDataTrait;

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
            "/deck-validation/standard",
            $this->getDeckData()['cards']
        );
        $response = $client->getResponse();
        $this->assertEquals(
            200,
            $response->getStatusCode()
        );
        $content = $this->getContent($client);
        $this->assertEquals(
            DeckValidator::VALID_DECK,
            $content['status']
        );
    }
}
