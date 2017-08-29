<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\User;
use AppBundle\Service\TokenManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Description of LoadTokenData
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class LoadTokenData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function load (ObjectManager $manager)
    {
        /** @var TokenManager $tokenManager */
        $tokenManager = $this->container->get('app.security.token_manager');

        $this->loadToken($tokenManager, 'admin', 'admin');
        $this->loadToken($tokenManager, 'guru', 'guru');
        $this->loadToken($tokenManager, 'user', 'user');
        $this->loadToken($tokenManager, 'user2', 'user2');
        $this->loadToken($tokenManager, 'pirate', 'pirate');
    }

    private function loadToken (TokenManager $tokenManager, string $value, string $username)
    {
        $token = $tokenManager->createToken($value, $this->getReference("user-$username"));
        $tokenManager->updateToken($token);
    }

    public function getOrder ()
    {
        return 2;
    }
}