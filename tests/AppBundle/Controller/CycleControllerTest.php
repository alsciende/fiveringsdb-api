<?php

declare(strict_types=1);

namespace Tests\AppBundle\Controller;

use AppBundle\Controller\CycleController;
use Tests\AppBundle\Controller\BaseApiControllerTest;

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
        $client->request('GET', "/cycles");
        $this->assertStandardGetMany($client);
    }
}
