<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Description of TokenPurgeCommand
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class TokenPurgeCommand extends ContainerAwareCommand
{
    protected function configure ()
    {
        $this
            ->setName('app:token:purge')
            ->setDescription("Purge expired tokens");
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $this
            ->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->createQuery('DELETE FROM AppBundle:Token t WHERE t.expiresAt < NOW()')
            ->execute();
    }
}