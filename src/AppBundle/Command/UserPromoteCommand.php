<?php
/**
 * Created by PhpStorm.
 * User: cedric
 * Date: 15/09/17
 * Time: 13:05
 */

namespace AppBundle\Command;

use AppBundle\Service\UserManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 */
class UserPromoteCommand extends ContainerAwareCommand
{
    protected function configure ()
    {
        $this
            ->setName('app:user:promote')
            ->setDescription("Promote user to a role")
            ->addArgument('username', InputArgument::REQUIRED, 'Name of user to promote')
            ->addArgument('role', InputArgument::REQUIRED, 'Role');
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $role = $input->getArgument('role');
        if (strpos($role, 'ROLE_') !== 0) {
            throw new \InvalidArgumentException('Role must start with ROLE_');
        }

        $userManager = $this->getContainer()->get(UserManager::class);
        $user = $userManager->findUserByUsername($input->getArgument('username'));
        if ($user === null) {
            throw new \InvalidArgumentException('No such user.');
        }

        $user->addRole($role);
        $userManager->updateUser($user);

        $output->writeln('User roles: ' . implode(', ', $user->getRoles()));
    }
}
