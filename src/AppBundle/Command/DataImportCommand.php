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
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        
        /* @var $validator \Symfony\Component\Validator\Validator\RecursiveValidator */
        $validator = $this->getContainer()->get('validator');
        
        /* @var $factory \Alsciende\CerealBundle\DeserializationJobFactory */
        $factory = new \Alsciende\CerealBundle\DeserializationJobFactory();

        $helper = $this->getHelper('question');

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

        foreach($types as $classname => $outputType) {

            $jobs = $factory->create($jsonDataPath, $classname);
            
            foreach($jobs as $job) {
                
                $job->run($em, $validator);
                
                if(!empty($job->getChanges())) {
                    $output->writeln("Currently the data is:");
                    dump($job->getOriginal());
                    $output->writeln("The incoming changes are:");
                    dump($job->getChanges());
                    $question = new \Symfony\Component\Console\Question\ConfirmationQuestion("Continue with these changes (Y/n)? ", true);
                    if(!$helper->ask($input, $output, $question)) {
                        $output->writeln("Operation aborted.");
                        return;
                    }
                }
            }

            $em->flush();
        }
    }

}
