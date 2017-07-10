<?php

namespace Tests\AppBundle\Controller\API\v1;

use Symfony\Component\HttpFoundation\Response;
use Tests\AppBundle\Controller\API\BaseApiControllerTest;

/**
 * Description of PackCardControllerTest
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class PackCardControllerTest extends BaseApiControllerTest
{

    public function testGetPackCards ()
    {
        $client = $this->getAnonymousClient();
        $client->request('GET', "/api/v1/pack-cards");
        $this->assertStandardGetMany($client);
    }

}
