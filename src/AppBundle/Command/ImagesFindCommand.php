<?php

namespace AppBundle\Command;

use AppBundle\Entity\PackCard;
use Curl\Curl;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 */
class ImagesFindCommand extends Command
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
            ->setName('app:images:find')
            ->setDescription("Find missing images from official servers");
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        // example http://lcg-cdn.fantasyflightgames.com/l5r/L5C01_1.jpg
        $baseURL = "http://lcg-cdn.fantasyflightgames.com/l5r";

        $curl = new Curl();
        $curl->setOpt(CURLOPT_NOBODY, true);

        $packCards = $this->entityManager->getRepository(PackCard::class)->findBy(['imageUrl' => null]);

        /** @var PackCard $packCard */
        foreach ($packCards as $packCard) {
            $url = sprintf(
                "%s/%s_%s.jpg",
                $baseURL,
                $packCard->getPack()->getFfgId(),
                $packCard->getPosition()
            );

            $curl->get($url);

            if ($curl->http_status_code === 200) {
                $packCard->setImageUrl($url);
                $output->writeln(sprintf('Found image for <info>%s</info>', $packCard->__toString()));
            } else {
                $output->writeln(sprintf('<error>Found no image for %s</error>', $packCard->__toString()));
            }
        }

        $this->entityManager->flush();
    }
}
