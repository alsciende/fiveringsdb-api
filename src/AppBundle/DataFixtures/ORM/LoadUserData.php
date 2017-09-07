<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Service\UserManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Description of LoadUserData
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{

    use ContainerAwareTrait;

    public function load (ObjectManager $manager)
    {
        /* @var $userManager UserManager */
        $userManager = $this->container->get('app.security.user_manager');

        $this->loadUser($userManager, 'admin', ['ROLE_ADMIN']);
        $this->loadUser($userManager, 'guru', ['ROLE_GURU']);
        $this->loadUser($userManager, 'user');
        $this->loadUser($userManager, 'user2');
        $this->loadUser($userManager, 'pirate');
    }

    private function loadUser (UserManager $userManager, $username, $roles = [])
    {
        $user = $userManager->createUser($username);
        foreach ($roles as $role) {
            $user->addRole($role);
        }
        $user->setEnabled(true);
        $userManager->updateUser($user);
        $this->addReference("user-$username", $user);
    }

    public function getOrder ()
    {
        return 1;
    }

}
