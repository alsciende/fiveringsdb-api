<?php

namespace Tests\AppBundle\Controller\API\v1;

use Symfony\Component\HttpFoundation\Response;
use Tests\AppBundle\Controller\API\BaseApiControllerTest;

/**
 * Description of ClanControllerTest
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class ClanControllerTest extends BaseApiControllerTest
{

    public function testGetClans ()
    {
        $client = $this->getAnonymousClient();
        $client->request('GET', "/api/v1/clans");
        $this->assertStandardGetMany($client);
    }

}
