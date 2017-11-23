<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Description of DataExportCommand
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DataExportCommand extends Command
{
    protected function configure ()
    {
        $this
            ->setName('app:data:export')
            ->setDescription("Export data from the database to JSON files");
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
    }
}
