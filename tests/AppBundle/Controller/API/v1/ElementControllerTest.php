<?php

namespace Tests\AppBundle\Controller\API\v1;

use Symfony\Component\HttpFoundation\Response;
use Tests\AppBundle\Controller\API\BaseApiControllerTest;

/**
 * Description of ElementControllerTest
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class ElementControllerTest extends BaseApiControllerTest
{

    public function testGetElements ()
    {
        $client = $this->getAnonymousClient();
        $client->request('GET', "/api/v1/elements");
        $this->assertStandardGetMany($client);
    }

}
