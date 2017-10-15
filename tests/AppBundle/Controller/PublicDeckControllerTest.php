<?php

declare(strict_types=1);

namespace Tests\AppBundle\Controller;

use AppBundle\Controller\DeckLineageController;
use AppBundle\Controller\PrivateDeckController;
use AppBundle\Controller\PublicDeckController;
use Tests\AppBundle\Controller\BaseApiControllerTest;
use Tests\AppBundle\DeckDataTrait;

/**
 * Test class for all the API endpoints that read/write decks
 *
 * @package Tests\AppBundle\Controller
 * @author Alsciende <alsciende@icloud.com>
 */
class PublicDeckControllerTest extends BaseApiControllerTest
{
    use DeckDataTrait;

    /**
     * @covers StrainController::postAction()
     */
    public function testStrainControllerPostAction ()
    {
        $client = $this->getClient('user');
        $this->sendJsonRequest(
            $client,
            'POST',
            "/strains"
        );
        $record = $this->assertStandardGetOne($client);
        return $record;
    }

    /**
     * @covers StrainDeckController::postAction()
     * @depends testStrainControllerPostAction
     */
    public function testStrainDeckControllerPostAction ($strain)
    {
        $client = $this->getClient('user');
        $strainId = $strain['id'];
        $this->sendJsonRequest(
            $client,
            'POST',
            "/strains/$strainId/decks",
            $this->getDeckData()
        );
        $record = $this->assertStandardGetOne($client);
        $this->assertEquals(
            'PHPUnit Test Deck',
            $record['name']
        );
        $this->assertEquals(
            35,
            count($record['cards'])
        );
        $this->assertEquals(
            '0.1',
            $record['version']
        );
        $this->assertArrayHasKey(
            'id',
            $record
        );
        return $record;
    }

    /**
     * @covers  DeckPublishController::postAction()
     * @depends testStrainDeckControllerPostAction
     */
    public function testDeckPublishControllerPostAction ($deck)
    {
        $client = $this->getClient('user');

        $id = $deck['strain']['id'];

        $this->sendJsonRequest(
            $client,
            'PATCH',
            "/strains/$id/publish"
        );
        $record = $this->assertStandardGetOne($client);
        $this->assertEquals(
            '1.0',
            $record['version']
        );
        return $record;
    }

    /**
     * @covers  PublicDeckController::getAction()
     * @depends testDeckPublishControllerPostAction
     */
    public function testPublicDeckControllerGetAction ($deck)
    {
        $client = $this->getClient();

        $id = $deck['id'];

        $this->sendJsonRequest(
            $client,
            'GET',
            "/decks/$id"
        );
        $record = $this->assertStandardGetOne($client);
        $this->assertEquals(
            $record['id'],
            $id
        );
    }

    /**
     * @covers  PublicDeckController::patchAction()
     * @depends testDeckPublishControllerPostAction
     */
    public function testPublicDeckControllerPatchActionPirate ($deck)
    {
        $client = $this->getClient('pirate');

        $id = $deck['id'];

        $this->sendJsonRequest(
            $client,
            'PATCH',
            "/decks/$id",
            [
                'name' => 'Updated Name',
                'description' => 'Updated description'
            ]
        );
        $this->assertEquals(
          403,
          $client->getResponse()->getStatusCode()
        );
    }

    /**
     * @covers  PublicDeckController::patchAction()
     * @depends testDeckPublishControllerPostAction
     */
    public function testPublicDeckControllerPatchAction ($deck)
    {
        $client = $this->getClient('user');

        $id = $deck['id'];

        $this->sendJsonRequest(
            $client,
            'PATCH',
            "/decks/$id",
            [
                'name' => 'Updated Name',
                'description' => 'Updated description'
            ]
        );
        $record = $this->assertStandardGetOne($client);
        $this->assertEquals(
            'Updated Name',
            $record['name']
        );
        $this->assertEquals(
            'Updated description',
            $record['description']
        );
    }

    /**
     * @covers  DeckLikeController::postAction()
     * @depends testDeckPublishControllerPostAction
     */
    public function testDeckLikeControllerPostAction ($deck)
    {
        $client = $this->getClient('user2');

        $id = $deck['id'];

        $this->sendJsonRequest(
            $client,
            'POST',
            "/decks/$id/likes"
        );
        $record = $this->assertStandardGetOne($client);
        $this->assertEquals(
            1,
            $record
        );
        return $deck;
    }

    /**
     * @covers  DeckLikeController::deleteAction()
     * @depends testDeckLikeControllerPostAction
     */
    public function testDeckLikeControllerDeleteAction ($deck)
    {
        $client = $this->getClient('user2');

        $id = $deck['id'];

        $this->sendJsonRequest(
            $client,
            'DELETE',
            "/decks/$id/likes"
        );
        $this->assertStandardGetNone($client);
    }

    /**
     * @covers  DeckCommentController::postAction()
     * @depends testDeckPublishControllerPostAction
     */
    public function testDeckCommentControllerPostAction ($deck)
    {
        $client = $this->getClient('user2');

        $id = $deck['id'];

        $this->sendJsonRequest(
            $client,
            'POST',
            "/decks/$id/comments",
            [
                'text' => 'Test comment text'
            ]
        );
        $record = $this->assertStandardGetOne($client);
        $this->assertArrayHasKey(
            'id',
            $record
        );
        $this->assertEquals(
            'Test comment text',
            $record['text']
        );
        return "/decks/$id/comments/".$record['id'];
    }

    /**
     * @covers  DeckCommentController::patchAction()
     * @depends testDeckCommentControllerPostAction
     */
    public function testDeckCommentControllerPatchActionPirate ($url)
    {
        $client = $this->getClient('pirate');

        $this->sendJsonRequest(
            $client,
            'PATCH',
            $url,
            [
                'text' => 'Updated text',
                'visible' => false
            ]
        );
        $this->assertEquals(
          403,
          $client->getResponse()->getStatusCode()
        );
    }

    /**
     * @covers  DeckCommentController::patchAction()
     * @depends testDeckCommentControllerPostAction
     */
    public function testDeckCommentControllerPatchAction ($url)
    {
        $client = $this->getClient('user2');

        $this->sendJsonRequest(
            $client,
            'PATCH',
            $url,
            [
                'text' => 'Updated text'
            ]
        );
        $record = $this->assertStandardGetOne($client);
        $this->assertEquals(
            'Updated text',
            $record['text']
        );
        $this->assertEquals(
            true,
            $record['visible']
        );
        return $record;
    }

    /**
     * @covers  DeckCommentController::visibilityPatchAction()
     * @depends testDeckCommentControllerPostAction
     */
    public function testDeckCommentVisibilityControllerPatchActionUser2 ($url)
    {
        $client = $this->getClient('user2');

        $this->sendJsonRequest(
            $client,
            'PATCH',
            "$url/visibility",
            [
                'visible' => false
            ]
        );
        $this->assertStatusCode($client, 403);
    }

    /**
     * @covers  DeckCommentController::visibilityPatchAction()
     * @depends testDeckCommentControllerPostAction
     */
    public function testDeckCommentVisibilityControllerPatchAction ($url)
    {
        $client = $this->getClient('user');

        $this->sendJsonRequest(
            $client,
            'PATCH',
            "$url/visibility",
            [
                'visible' => false
            ]
        );
        $record = $this->assertStandardGetOne($client);
        $this->assertEquals(
            false,
            $record['visible']
        );
    }

    /**
     * @covers  PublicDeckController::deleteAction()
     * @depends testDeckPublishControllerPostAction
     */
    public function testPublicDeckControllerDeleteActionPirate ($deck)
    {
        $client = $this->getClient('pirate');

        $id = $deck['id'];

        $this->sendJsonRequest(
            $client,
            'DELETE',
            "/decks/$id"
        );
        $this->assertEquals(
          403,
          $client->getResponse()->getStatusCode()
        );
    }

    /**
     * @covers  PublicDeckController::deleteAction()
     * @depends testDeckPublishControllerPostAction
     */
    public function testPublicDeckControllerDeleteAction ($deck)
    {
        $client = $this->getClient('user');

        $id = $deck['id'];

        $this->sendJsonRequest(
            $client,
            'DELETE',
            "/decks/$id"
        );
        $this->assertUnsuccessful($client);
    }

    /**
     * @covers StrainController::deleteAction()
     * @depends testStrainControllerPostAction
     */
    public function testStrainControllerDeleteAction ($strain)
    {
        $client = $this->getClient('user');
        $id = $strain['id'];
        $this->sendJsonRequest(
            $client,
            'DELETE',
            "/strains/$id"
        );
        $this->assertStandardGetNone($client);
    }
}
