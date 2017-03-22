<?php

namespace AppBundle\DataFixtures\ORM;

use Alsciende\SecurityBundle\Service\UserManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Description of LoadUserData
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    
    /**
     * @var ContainerInterface
     */
    private $container;
    
    public function load(ObjectManager $manager)
    {
        /* @var $userManager UserManager */
        $userManager = $this->container->get('alsciende_security.user_manager');
        $userAdmin = $userManager->createUser('admin', 'test');

        $userManager->updateUser($userAdmin);

        $this->addReference('admin-user', $userAdmin);
    }
    
    public function getOrder()
    {
        return 1;
    }
}
