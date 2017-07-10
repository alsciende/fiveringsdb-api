<?php

namespace Tests\AppBundle\Controller\API\v1;

use Symfony\Component\HttpFoundation\Response;
use Tests\AppBundle\Controller\API\BaseApiControllerTest;

/**
 * Description of CardControllerTest
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class CardControllerTest extends BaseApiControllerTest
{

    public function testGetCards ()
    {
        $client = $this->getAnonymousClient();
        $client->request('GET', "/api/v1/cards");
        $this->assertStandardGetMany($client);
    }

    public function testGetOneCard ()
    {
        $client = $this->getAnonymousClient();
        $client->request('GET', "/api/v1/cards/above-question");
        $this->assertStandardGetOne($client);
    }
}
