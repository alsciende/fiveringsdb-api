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
                ->addOption('separate', 's', InputOption::VALUE_NONE, "Create one JSON file per entity")
                ->addArgument('entity', InputArgument::REQUIRED, "Name of the entity to export")
        ;
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $entityName = $input->getArgument('entity');
        $separate = $input->getOption('separate');
        
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $repository = $em->getRepository($entityName);
        if(!$repository) {
            throw new Exception("Invalid Entity name $entityName");
        }
        
        $list = $repository->findAll();
        dump($list);
    }

}
