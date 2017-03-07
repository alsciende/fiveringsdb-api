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
        /* @var $serializer \Alsciende\DoctrineSerializerBundle\Serializer */
        $serializer = $this->getContainer()->get('alsciende.doctrine_serializer.serializer');
        $result = $serializer->import();

        foreach ($result as $fragment) {
            if (!empty($fragment->getChanges())) {
                $output->writeln("The data was:");
                dump($fragment->getOriginal());
                $output->writeln("The changes that were applied are:");
                dump($fragment->getChanges());
            } else {
                $output->writeln("No change found in " . $fragment->getPath());
            }
        }
    }

}
