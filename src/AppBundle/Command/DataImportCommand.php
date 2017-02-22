<?php

namespace AppBundle\Command;

use Alsciende\CerealBundle\AlsciendeCerealBundle;
use Alsciende\CerealBundle\DeserializationJobFactory;
use AppBundle\Entity\Card;
use AppBundle\Entity\Clan;
use AppBundle\Entity\Cycle;
use AppBundle\Entity\Pack;
use AppBundle\Entity\PackSlot;
use AppBundle\Entity\Type;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Validator\Validator\RecursiveValidator;

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
        /* @var $em EntityManager */
        $em = $this->getContainer()->get('doctrine')->getEntityManager();

        /* @var $validator RecursiveValidator */
        $validator = $this->getContainer()->get('validator');

        /* @var $factory DeserializationJobFactory */
        $factory = new DeserializationJobFactory();

        $helper = $this->getHelper('question');

        $rootDir = $this->getContainer()->get('kernel')->getRootDir();
        $jsonPath = $this->getContainer()->getParameter('json_data_path');

        $fs = new Filesystem();
        if (!$fs->isAbsolutePath($jsonPath)) {
            $jsonPath = $rootDir . '/' . $jsonPath;
        }
        $jsonDataPath = realpath($jsonPath);

        $types = [
            Type::class => AlsciendeCerealBundle::SINGLE_FILE | AlsciendeCerealBundle::MULTIPLE_DATA,
            Clan::class => AlsciendeCerealBundle::SINGLE_FILE | AlsciendeCerealBundle::MULTIPLE_DATA,
            Cycle::class => AlsciendeCerealBundle::SINGLE_FILE | AlsciendeCerealBundle::MULTIPLE_DATA,
            Card::class => AlsciendeCerealBundle::MULTIPLE_FILES | AlsciendeCerealBundle::SINGLE_DATA,
            Cycle::class => AlsciendeCerealBundle::SINGLE_FILE | AlsciendeCerealBundle::MULTIPLE_DATA,
            Pack::class => AlsciendeCerealBundle::SINGLE_FILE | AlsciendeCerealBundle::MULTIPLE_DATA,
            PackSlot::class => AlsciendeCerealBundle::MULTIPLE_FILES | AlsciendeCerealBundle::MULTIPLE_DATA,
        ];

        $output->writeln("Loading data from " . $jsonDataPath);

        foreach ($types as $classname => $outputType) {
            $jobs = $factory->create($jsonDataPath, $classname, $outputType);
            foreach ($jobs as $job) {
                $job->run($em, $validator);
                if (!empty($job->getChanges())) {
                    $output->writeln("Currently the data is:");
                    dump($job->getOriginal());
                    $output->writeln("The incoming changes are:");
                    dump($job->getChanges());
                    $question = new ConfirmationQuestion("Continue with these changes (Y/n)? ", true);
                    if (!$helper->ask($input, $output, $question)) {
                        $output->writeln("Operation aborted.");
                        return;
                    }
                    $output->writeln("<comment>Data modified</comment>");
                } else {
                    $output->writeln("No change found in ".$job->getFilepath()."\n");
                }
            }
            $em->flush();
        }
    }

}
