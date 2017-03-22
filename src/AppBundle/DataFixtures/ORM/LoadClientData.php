<?php

namespace AppBundle\DataFixtures\ORM;

use Alsciende\SecurityBundle\Entity\Client;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Description of LoadClientData
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class LoadClientData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load (ObjectManager $manager)
    {
        $client = new Client();
        $client->setRedirectUris(array('http://localhost/redirect'));
        $client->setAllowedGrantTypes(array('authorization_code', 'refresh_tokens'));
        $client->setName("Test Client");
        $client->setEmail("test-client@oauth.net");

        $manager->persist($client);
        $manager->flush();
        
        $this->addReference('oauth-client', $client);
    }

    public function getOrder()
    {
        return 1;
    }
}
