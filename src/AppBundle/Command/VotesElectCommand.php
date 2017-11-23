<?php

namespace AppBundle\Command;

use AppBundle\Service\FeatureManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Description of VotesElectCommand
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class VotesElectCommand extends Command
{
    /** @var FeatureManager $featureManager */
    private $featureManager;

    public function __construct ($name = null, FeatureManager $featureManager)
    {
        parent::__construct($name);
        $this->featureManager = $featureManager;
    }

    protected function configure ()
    {
        $this
            ->setName('app:votes:elect')
            ->setDescription("Elect the featured decks");
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $this->featureManager->electFeatures();
    }
}