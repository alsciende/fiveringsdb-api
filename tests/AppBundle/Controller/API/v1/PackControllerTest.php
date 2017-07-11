<?php

declare(strict_types=1);

namespace Tests\AppBundle\Controller\API\v1;

use AppBundle\Controller\API\v1\PackController;
use Tests\AppBundle\Controller\API\BaseApiControllerTest;

/**
 * Description of PackControllerTest
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class PackControllerTest extends BaseApiControllerTest
{
    /**
     * @covers PackController::listAction()
     */
    public function testPackControllerListAction ()
    {
        $client = $this->getAnonymousClient();
        $client->request('GET', "/api/v1/packs");
        $this->assertStandardGetMany($client);
    }
}
