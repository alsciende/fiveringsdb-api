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

        /* @var $cereal \Alsciende\CerealBundle\Service\Cereal */
        $cereal = $this->getContainer()->get('alsciende_cereal.cereal');

        $rootDir = $this->getContainer()->get('kernel')->getRootDir();
        $jsonPath = $this->getContainer()->getParameter('json_data_path');

        $fs = new \Symfony\Component\Filesystem\Filesystem();
        if(!$fs->isAbsolutePath($jsonPath)) {
            $jsonDataPath = $rootDir . '/' . $jsonPath;
        } else {
            $jsonDataPath = $jsonPath;
        }

        $types = [
            \AppBundle\Entity\Type::class => \Alsciende\CerealBundle\AlsciendeCerealBundle::OUTPUT_COMBINED,
            \AppBundle\Entity\Clan::class => \Alsciende\CerealBundle\AlsciendeCerealBundle::OUTPUT_COMBINED,
            \AppBundle\Entity\Cycle::class => \Alsciende\CerealBundle\AlsciendeCerealBundle::OUTPUT_COMBINED,
            \AppBundle\Entity\Pack::class => \Alsciende\CerealBundle\AlsciendeCerealBundle::OUTPUT_COMBINED,
            \AppBundle\Entity\Card::class => \Alsciende\CerealBundle\AlsciendeCerealBundle::OUTPUT_SPLIT
        ];

        foreach($types as $className => $outputType) {
            $entities = $cereal->import($jsonDataPath, $className);
            $output->writeln("Imported <info>" . count($entities) . "</info> objects of type <info>$className</info>");
        }
    }

}
