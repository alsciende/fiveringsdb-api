<?php

namespace AppBundle\Command;

use Alsciende\SerializerBundle\Model\Source;
use Alsciende\SerializerBundle\Service\ImportingService;
use Alsciende\SerializerBundle\Service\MergingService;
use Alsciende\SerializerBundle\Service\ScanningService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Description of DataImportCommand
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DataImportCommand extends Command
{
    /** @var ImportingService $importer */
    private $importer;

    /** @var string $jsonDataPath */
    private $jsonDataPath;

    public function __construct (
        $name = null,
        ImportingService $importer,
        LoggerInterface $logger,
        $jsonDataPath
    ) {
        parent::__construct($name);
        $this->importer = $importer;
        $this->jsonDataPath = $jsonDataPath;

        $this->importer->setLogger($logger);
    }

    protected function configure ()
    {
        $this
            ->setName('app:data:import')
            ->setDescription("Import data from JSON files to the database");
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $this->importer->import($this->jsonDataPath, true);
    }
}
