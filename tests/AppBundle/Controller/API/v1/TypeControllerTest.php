<?php

namespace Tests\AppBundle\Controller\API\v1;

use Symfony\Component\HttpFoundation\Response;
use Tests\AppBundle\Controller\API\BaseApiControllerTest;

/**
 * Description of TypeControllerTest
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class TypeControllerTest extends BaseApiControllerTest
{

    public function testGetTypes ()
    {
        $client = $this->getAnonymousClient();
        $client->request('GET', "/api/v1/types");
        $this->assertStandardGetMany($client);
    }

}
