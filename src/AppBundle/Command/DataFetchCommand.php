<?php

namespace AppBundle\Command;

use AppBundle\Entity\Pack;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

/**
 * Description of DataFetchCommand
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DataFetchCommand extends Command
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
            ->setName('app:data:fetch')
            ->setDescription("Fetch a data file for a pack from cardgamedb.com");
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $packRepository = $this->entityManager->getRepository(Pack::class);

        $packs = $packRepository->findBy([], ['ffgId' => 'ASC']);
        $arrayMap = array_map(
            function (Pack $pack) {
                return $pack->getId();
            },
            $packs
        );
        $choiceQuestion = new ChoiceQuestion(
            'Pack: ',
            $arrayMap
        );
        $id = $this->getHelper('question')->ask($input, $output, $choiceQuestion);
        $pack = $this->entityManager->find(Pack::class, $id);

        if ($pack instanceof Pack) {
            $command = sprintf(
                'curl -o l5r-db.js http://www.cardgamedb.com/deckbuilders/legendofthefiverings/database/%s-db.jgz',
                $pack->getFfgId()
            );
            exec($command);
        }
    }
}
