<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Description of DataFetchCommand
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DataFetchCommand extends ContainerAwareCommand
{
    protected function configure ()
    {
        $this
            ->setName('app:data:fetch')
            ->setDescription("Fetch the latest javascript data file from cardgamedb.com");
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $format = 'http://www.cardgamedb.com/deckbuilders/legendofthefiverings/database/l5r-db-%s.jgz';

        $version = '1.0';

        exec(sprintf('curl -o l5r-db.js ' . $format, $version));
    }
}