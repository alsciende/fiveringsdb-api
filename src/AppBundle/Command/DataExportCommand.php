<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Description of DataExportCommand
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DataExportCommand extends ContainerAwareCommand
{

    protected function configure ()
    {
        $this
            ->setName('app:data:export')
            ->setDescription("Export data from the database to JSON files")
        ;
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {

    }

}
