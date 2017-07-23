<?php

declare(strict_types=1);

namespace Tests\AppBundle\Controller;

use AppBundle\Controller\CardReviewController;
use Tests\AppBundle\Controller\BaseApiControllerTest;

class CardReviewControllerTest extends BaseApiControllerTest
{
    /**
     * @covers CardReviewController::postAction()
     */
    public function testCardReviewControllerPostAction()
    {
        $client = $this->getClient('user');

        $this->sendJsonRequest(
            $client,
            'POST',
            "/cards/above-question/reviews",
            [
                'text' => 'Test review text'
            ]
        );
        $record = $this->assertStandardGetOne($client);
        $this->assertArrayHasKey(
            'id',
            $record
        );
        $this->assertEquals(
            'Test review text',
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
     * @covers CardReviewController::listAction()
     * @depends testCardReviewControllerPostAction
     */
    public function testCardReviewControllerListAction()
    {
        $client = $this->getClient();
        $this->sendJsonRequest(
            $client,
            'GET',
            "/cards/above-question/reviews"
        );
        $records = $this->assertStandardGetMany($client);
        $this->assertGreaterThanOrEqual(
            1,
            count($records)
        );
    }

    /**
     * @covers CardReviewController::getAction()
     * @depends testCardReviewControllerPostAction
     */
    public function testCardReviewControllerGetAction($review)
    {
        $client = $this->getClient();
        $id = $review['id'];
        $this->sendJsonRequest(
            $client,
            'GET',
            "/cards/above-question/reviews/$id"
        );
        $record = $this->assertStandardGetOne($client);
        $this->assertEquals(
            $record['id'],
            $review['id']
        );
    }

    /**
     * @covers CardReviewController::patchAction()
     * @depends testCardReviewControllerPostAction
     */
    public function testCardReviewControllerPatchAction($review)
    {
        $client = $this->getClient('user');
        $id = $review['id'];
        $this->sendJsonRequest(
            $client,
            'PATCH',
            "/cards/above-question/reviews/$id",
            [
                'text' => 'Updated review text'
            ]
        );
        $record = $this->assertStandardGetOne($client);
        $this->assertEquals(
            $review['id'],
            $record['id']
        );
        $this->assertEquals(
            $review['card_code'],
            $record['card_code']
        );
        $this->assertEquals(
            'Updated review text',
            $record['text']
        );
    }
}
