<?php

declare(strict_types=1);

namespace Tests\AppBundle\Controller;

use AppBundle\Controller\CardRulingController;
use Tests\AppBundle\Controller\BaseApiControllerTest;

class CardRulingControllerTest extends BaseApiControllerTest
{
    /**
     * @covers CardRulingController::postAction()
     */
    public function testCardRulingControllerPostAction()
    {
        $client = $this->getAuthenticatedClient('guru', 'guru');

        $this->sendJsonRequest(
            $client,
            'POST',
            "/cards/above-question/rulings",
            [
                'text' => 'Test ruling text'
            ]
        );
        $record = $this->assertStandardGetOne($client);
        $this->assertArrayHasKey(
            'id',
            $record
        );
        $this->assertEquals(
            'Test ruling text',
            $record['text']
        );
        $this->assertEquals(
            'above-question',
            $record['card_code']
        );
        $this->assertArrayHasKey(
            'user_id',
            $record
        );
        return $record;
    }

    /**
     * @covers CardRulingController::listAction()
     * @depends testCardRulingControllerPostAction
     */
    public function testCardRulingControllerListAction()
    {
        $client = $this->getAnonymousClient();
        $this->sendJsonRequest(
            $client,
            'GET',
            "/cards/above-question/rulings"
        );
        $records = $this->assertStandardGetMany($client);
        $this->assertGreaterThanOrEqual(
            1,
            count($records)
        );
    }

    /**
     * @covers CardRulingController::getAction()
     * @depends testCardRulingControllerPostAction
     */
    public function testCardRulingControllerGetAction($ruling)
    {
        $client = $this->getAnonymousClient();
        $id = $ruling['id'];
        $this->sendJsonRequest(
            $client,
            'GET',
            "/cards/above-question/rulings/$id"
        );
        $record = $this->assertStandardGetOne($client);
        $this->assertEquals(
            $record['id'],
            $ruling['id']
        );
    }

    /**
     * @covers CardRulingController::patchAction()
     * @depends testCardRulingControllerPostAction
     */
    public function testCardRulingControllerPatchAction($ruling)
    {
        $client = $this->getAuthenticatedClient('guru', 'guru   ');
        $id = $ruling['id'];
        $this->sendJsonRequest(
            $client,
            'PATCH',
            "/cards/above-question/rulings/$id",
            [
                'text' => 'Updated ruling text'
            ]
        );
        $record = $this->assertStandardGetOne($client);
        $this->assertEquals(
            $ruling['id'],
            $record['id']
        );
        $this->assertEquals(
            $ruling['card_code'],
            $record['card_code']
        );
        $this->assertEquals(
            'Updated ruling text',
            $record['text']
        );
    }
}
