<?php

declare(strict_types=1);

namespace Tests\Unit\AppBundle\Controller;

use AppBundle\Controller\DeckLineageController;
use AppBundle\Controller\PrivateDeckController;
use AppBundle\Controller\PublicDeckController;
use Tests\Unit\AppBundle\Controller\BaseApiControllerTest;
use Tests\Unit\AppBundle\DeckDataTrait;

/**
 * Test class for all the API endpoints that read/write decks
 *
 * @package Tests\Unit\AppBundle\Controller
 * @author Alsciende <alsciende@icloud.com>
 */
class DeckCopyControllerTest extends BaseApiControllerTest
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
     * @depends testStrainDeckControllerPostAction
     */
    public function testDeckCopyControllerPostAction ($deck)
    {
        $client = $this->getClient('user2');

        $this->sendJsonRequest(
            $client,
            'POST',
            "/strains",
            [
              'origin' => $deck['id']
            ]
        );
        $response = $client->getResponse();
        $this->assertEquals(
            200,
            $response->getStatusCode()
        );
        $record = $this->assertStandardGetOne($client);
        $this->assertArrayHasKey(
          'head',
          $record
        );
        $head = $record['head'];
        $this->assertEquals(
            'Copy of PHPUnit Test Deck',
            $head['name']
        );
        $this->assertEquals(
            35,
            count($head['cards'])
        );
        $this->assertEquals(
            '0.1',
            $head['version']
        );
        $this->assertNotEquals(
            $head['id'],
            $deck['id']
        );
    }
}
