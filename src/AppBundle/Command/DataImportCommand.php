<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Description of DataImportCommand
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DataImportCommand extends Command
{

    protected function configure ()
    {
                $this
                ->setName('app:data:import')
                ->setDescription("Import data from JSON files to the database")
        ;

    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        
    }

}
