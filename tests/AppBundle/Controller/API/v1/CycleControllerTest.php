<?php

declare(strict_types=1);

namespace Tests\AppBundle\Controller\API\v1;

use AppBundle\Controller\API\v1\CycleController;
use Tests\AppBundle\Controller\API\BaseApiControllerTest;

/**
 * Description of CycleControllerTest
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class CycleControllerTest extends BaseApiControllerTest
{
    /**
     * @covers CycleController::listAction()
     */
    public function testCycleControllerListAction ()
    {
        $client = $this->getAnonymousClient();
        $client->request('GET', "/api/v1/cycles");
        $this->assertStandardGetMany($client);
    }
}
