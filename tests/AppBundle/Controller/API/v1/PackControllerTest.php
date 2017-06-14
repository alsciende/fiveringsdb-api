<?php

namespace Tests\AppBundle\Controller\API\v1;

use Symfony\Component\HttpFoundation\Response;
use Tests\AppBundle\Controller\API\BaseApiControllerTest;

/**
 * Description of PackControllerTest
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class PackControllerTest extends BaseApiControllerTest
{

    public function testGetPacks ()
    {
        $client = $this->getAnonymousClient();
        $client->request('GET', "/api/v1/packs");
        $this->assertStandardGetMany($client);
    }

}
