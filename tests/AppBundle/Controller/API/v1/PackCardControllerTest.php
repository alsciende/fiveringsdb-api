<?php

declare(strict_types=1);

namespace Tests\AppBundle\Controller\API\v1;

use AppBundle\Controller\API\v1\PackCardController;
use Tests\AppBundle\Controller\API\BaseApiControllerTest;

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
        $client = $this->getAnonymousClient();
        $client->request('GET', "/api/v1/pack-cards");
        $this->assertStandardGetMany($client);
    }
}
