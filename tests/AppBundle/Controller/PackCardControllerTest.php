<?php

declare(strict_types=1);

namespace Tests\AppBundle\Controller;

use AppBundle\Controller\PackCardController;
use Tests\AppBundle\Controller\BaseApiControllerTest;

/**
 * Description of PackCardControllerTest
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class PackCardControllerTest extends BaseApiControllerTest
{
    /**
     * @covers PackCardController::listAction()
     */
    public function testPackCardControllerListAction ()
    {
        $client = $this->getClient();
        $client->request('GET', "/pack-cards");
        $this->assertStandardGetMany($client);
    }
}
