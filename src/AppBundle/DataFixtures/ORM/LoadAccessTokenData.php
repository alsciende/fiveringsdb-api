<?php

namespace AppBundle\DataFixtures\ORM;

use Alsciende\SecurityBundle\Entity\AccessToken;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Description of LoadAccessTokenData
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class LoadAccessTokenData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load (ObjectManager $manager)
    {
        $token = new AccessToken();
        $token->setToken("TEST_TOKEN");
        $token->setClient($this->getReference('oauth-client'));
        $token->setExpiresAt(null);
        $token->setScope(null);
        $token->setUser($this->getReference('admin-user'));

        $manager->persist($token);
        $manager->flush();
        
        $this->addReference('access-token', $token);
    }

    public function getOrder()
    {
        return 2;
    }
}
