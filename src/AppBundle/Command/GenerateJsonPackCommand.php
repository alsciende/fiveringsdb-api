<?php

namespace AppBundle\Command;

use AppBundle\Entity\Pack;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 */
class GenerateJsonPackCommand extends Command
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
            ->setName('app:generate:pack')
            ->setDescription("Generate json file for a pack")
            ->addArgument('id', InputArgument::REQUIRED, 'Id of the pack');
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $pack = $this->entityManager->find(Pack::class, $input->getArgument('id'));
        if (!$pack instanceof Pack) {
            throw new \Exception('Pack Not Found');
        }

        $packCards = [];
        $start = $pack->getSize() * ($pack->getPosition() - 1);
        $relativePosition = 0;
        while ($relativePosition++ < $pack->getSize()) {
            $position = strval($start + $relativePosition);
            $packCards[] = [
                "card_id"     => "",
                "flavor"      => "",
                "illustrator" => "",
                "image_url"   => "http://lcg-cdn.fantasyflightgames.com/l5r/" . $pack->getFfgId() . "_" . $position . ".jpg",
                "position"    => $position,
                "quantity"    => 3,
            ];
        }

        file_put_contents($pack->getId() . ".json", json_encode($packCards, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }
}
