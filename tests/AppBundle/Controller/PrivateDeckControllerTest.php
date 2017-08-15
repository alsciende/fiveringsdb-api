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
class PrivateDeckControllerTest extends BaseApiControllerTest
{
    use DeckDataTrait;

    /**
     * @covers StrainController::postAction()
     */
    public function testStrainControllerPostActionAnonymous ()
    {
        $client = $this->getClient();
        $this->sendJsonRequest(
            $client,
            'POST',
            "/strains"
        );
        $this->assertStatusCode($client, 401);
    }

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
    public function testStrainDeckControllerPostActionPirate ($strain)
    {
        $client = $this->getClient('pirate');
        $strainId = $strain['id'];
        $this->sendJsonRequest(
            $client,
            'POST',
            "/strains/$strainId/decks",
            $this->getDeckData()
        );
        $this->assertStatusCode($client, 403);
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
            34,
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
     * @covers StrainController::listAction()
     * @depends testStrainControllerPostAction
     */
    public function testStrainControllerListAction ()
    {
        $client = $this->getClient('user');
        $this->sendJsonRequest(
            $client,
            'GET',
            "/strains"
        );
        $records = $this->assertStandardGetMany($client);
    }

    /**
     * @covers StrainDeckController::postAction()
     * @depends testStrainDeckControllerPostAction
     */
    public function testStrainDeckControllerPostAction2 ($deck)
    {
        $client = $this->getClient('user');
        $strainId = $deck['strain'];
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
            34,
            count($record['cards'])
        );
        $this->assertEquals(
            '0.2',
            $record['version']
        );
        $this->assertArrayHasKey(
            'id',
            $record
        );
        return $record;
    }

    /**
     * @covers  StrainDeckController::listAction()
     * @depends testStrainDeckControllerPostAction2
     */
    public function testStrainDeckControllerListAction ($deck)
    {
        $client = $this->getClient('user');

        $strainId = $deck['strain'];

        $this->sendJsonRequest(
            $client,
            'GET',
            "/strains/$strainId/decks"
        );
        $records = $this->assertStandardGetMany($client);
        $this->assertEquals(
            2,
            count($records)
        );
        return $records;
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
