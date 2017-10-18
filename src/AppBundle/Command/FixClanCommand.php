<?php
/**
 * Created by PhpStorm.
 * User: cedric
 * Date: 02/10/17
 * Time: 09:41
 */

namespace AppBundle\Command;

use AppBundle\Entity\Deck;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


/**
 */
class FixClanCommand extends ContainerAwareCommand
{
    protected function configure ()
    {
        $this
            ->setName('app:fix:clan')
            ->setDescription("Fix the primary and secondary clans of each deck");
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');

        /** @var Deck[] $decks */
        $decks = $entityManager->getRepository(Deck::class)->findAll();

        foreach ($decks as $deck) {
            $deck->setPrimaryClan($deck->getDeckCards()->findPrimaryClan());
            $deck->setSecondaryClan($deck->getDeckCards()->findSecondaryClan($deck->getPrimaryClan()));
        }

        $entityManager->flush();
    }
}
