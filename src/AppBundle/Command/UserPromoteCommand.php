<?php

namespace AppBundle\Command;

use AppBundle\Service\UserManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 */
class UserPromoteCommand extends Command
{
    /** @var UserManager $userManager */
    private $userManager;

    public function __construct ($name = null, UserManager $userManager)
    {
        parent::__construct($name);
        $this->userManager = $userManager;
    }

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

        $user = $this->userManager->findUserByUsername($input->getArgument('username'));
        if ($user === null) {
            throw new \InvalidArgumentException('No such user.');
        }

        $user->addRole($role);
        $this->userManager->updateUser($user);

        $output->writeln('User roles: ' . implode(', ', $user->getRoles()));
    }
}
