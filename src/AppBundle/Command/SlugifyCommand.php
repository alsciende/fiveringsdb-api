<?php

namespace AppBundle\Command;

use Cocur\Slugify\Slugify;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Description of SlugifyCommand
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class SlugifyCommand extends ContainerAwareCommand
{

    protected function configure ()
    {
        $this
            ->setName('app:slugify')
            ->setDescription("Converts a string into a slug.")
            ->addArgument("string", InputArgument::REQUIRED, "String to convert")
            ->addOption("ruleset", "r", InputOption::VALUE_REQUIRED, "Ruleset for conversion")
        ;
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $string = $input->getArgument('string');
        $ruleset = $input->getOption('ruleset');

        $slugify = new Slugify();
        if ($ruleset) {
            $slugify->activateRuleset($ruleset);
        }
        $output->writeln($slugify->slugify($string));
    }

}
