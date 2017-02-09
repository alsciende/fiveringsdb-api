<?php

namespace Alsciende\SecurityBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Description of CreateClientCommand
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class ClientDeleteCommand extends ContainerAwareCommand
{

    protected function configure ()
    {
        $this
                ->setName('alsciende_security:client:delete')
                ->setDescription('Deletes a client')
                ->addArgument('client_id', \Symfony\Component\Console\Input\InputArgument::REQUIRED, "Id of the client to delete")
        ;
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $clientId = $input->getArgument('client_id');
        
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        
        $client = $em->getRepository('AlsciendeSecurityBundle:Client')->find($clientId);
        
        $em->remove($client);
        $em->flush();
        
        $output->writeln("Done");
    }

}
