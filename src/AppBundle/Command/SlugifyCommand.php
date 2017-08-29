<?php

namespace AppBundle\Command;

/**
 * Description of SlugifyCommand
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class SlugifyCommand extends \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
{

    protected function configure ()
    {
        $this
            ->setName('app:slugify')
            ->setDescription("Converts a string into a slug.")
            ->addArgument("string", \Symfony\Component\Console\Input\InputArgument::REQUIRED, "String to convert")
            ->addOption("ruleset", "r", \Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, "Ruleset for conversion")
        ;
    }

    protected function execute (\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
    {
        $string = $input->getArgument('string');
        $ruleset = $input->getOption('ruleset');

        $slugify = new \Cocur\Slugify\Slugify();
        if ($ruleset) {
            $slugify->activateRuleset($ruleset);
        }
        $output->writeln($slugify->slugify($string));
    }

}
