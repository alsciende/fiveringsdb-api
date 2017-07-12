<?php

declare(strict_types=1);

namespace Tests\AppBundle\Controller;

use AppBundle\Controller\CardController;
use Tests\AppBundle\Controller\BaseApiControllerTest;

/**
 * Description of CardControllerTest
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class CardControllerTest extends BaseApiControllerTest
{
    /**
     * @covers CardController::listAction()
     */
    public function testCardControllerListAction ()
    {
        $client = $this->getAnonymousClient();
        $client->request('GET', '/cards');
        $records = $this->assertStandardGetMany($client);
        return $records;
    }

    /**
     * @covers  CardController::getAction()
     * @depends testCardControllerListAction
     */
    public function testCardControllerGetAction ($cards)
    {
        $client = $this->getAnonymousClient();
        $client->request('GET', '/cards/' . $cards[0]['code']);
        $this->assertStandardGetOne($client);
    }
}
