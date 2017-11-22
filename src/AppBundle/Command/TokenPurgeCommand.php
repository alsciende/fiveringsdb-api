<?php

namespace AppBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Description of TokenPurgeCommand
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class TokenPurgeCommand extends ContainerAwareCommand
{
    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    public function __construct ($name = null, EntityManagerInterface $entityManager)
    {
        parent::__construct($name);
        $this->entityManager = $entityManager;
    }

    protected function configure ()
    {
        $this
            ->setName('app:token:purge')
            ->setDescription("Purge expired tokens");
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $this
            ->entityManager
            ->createQuery('DELETE FROM AppBundle:Token t WHERE t.expiresAt < NOW()')
            ->execute();
    }
}