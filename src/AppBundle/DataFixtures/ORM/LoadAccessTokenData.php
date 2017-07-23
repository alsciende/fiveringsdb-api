<?php

namespace AppBundle\DataFixtures\ORM;

use Alsciende\SecurityBundle\Entity\AccessToken;
use Alsciende\SecurityBundle\Entity\Client;
use AppBundle\Entity\User;
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
        $client = $this->getReference('oauth-client');
        $this->loadAccessToken($manager, $client, 'admin');
        $this->loadAccessToken($manager, $client, 'guru');
        $this->loadAccessToken($manager, $client, 'user');
        $this->loadAccessToken($manager, $client, 'user2');
        $this->loadAccessToken($manager, $client, 'pirate');
    }

    public function loadAccessToken(ObjectManager $manager, Client $client, string $username)
    {
        $user = $this->getReference("user-".$username);

        $token = new AccessToken();
        $token->setToken($username."-access-token");
        $token->setClient($client);
        $token->setExpiresAt(null);
        $token->setScope(null);
        $token->setUser($user);

        $manager->persist($token);
        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}
