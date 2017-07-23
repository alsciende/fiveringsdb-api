<?php

declare(strict_types=1);

namespace Tests\AppBundle\Controller;

use AppBundle\Controller\PackController;
use Tests\AppBundle\Controller\BaseApiControllerTest;

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
        $client = $this->getClient();
        $client->request('GET', "/packs");
        $this->assertStandardGetMany($client);
    }
}
