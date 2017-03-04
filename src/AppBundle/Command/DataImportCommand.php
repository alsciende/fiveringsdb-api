<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Description of DataImportCommand
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DataImportCommand extends ContainerAwareCommand
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
        $serializer = $this->getContainer()->get('alsciende.doctrine_serializer.serializer');
        $result = $serializer->import();

        foreach ($result as $changeSet) {
            if (!empty($changeSet->getChanges())) {
                $output->writeln("The data was:");
                dump($changeSet->getOriginal());
                $output->writeln("The changes that were applied are:");
                dump($changeSet->getChanges());
            } else {
                $output->writeln("No change found in " . $changeSet->getFilepath());
            }
        }
    }

}
