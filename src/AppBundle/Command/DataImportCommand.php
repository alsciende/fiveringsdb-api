<?php

namespace AppBundle\Command;

use Alsciende\SerializerBundle\Service\ImportingService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

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

    /** @var string $environment */
    private $environment;

    public function __construct (
        $name = null,
        ImportingService $importer,
        $jsonDataPath,
        $environment
    ) {
        parent::__construct($name);
        $this->importer = $importer;
        $this->jsonDataPath = $jsonDataPath;
        $this->environment = $environment;
    }

    protected function configure ()
    {
        $this
            ->setName('app:data:import')
            ->setDescription("Import data from JSON files to the database");
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title(sprintf(
            "Importing data for the <info>%s</info> environment",
            $this->environment
        ));

        $this->importer->setLogger(new ConsoleLogger($output));
        $this->importer->import($this->jsonDataPath, true);

        $io->success('Data imported.');
    }
}
