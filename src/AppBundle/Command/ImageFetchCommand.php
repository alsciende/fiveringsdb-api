<?php
/**
 * Created by PhpStorm.
 * User: cedric
 * Date: 24/08/17
 * Time: 09:08
 */

namespace AppBundle\Command;

use AppBundle\Entity\PackCard;
use Curl\Curl;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 */
class ImageFetchCommand extends ContainerAwareCommand
{
    protected function configure ()
    {
        $this
            ->setName('app:images:find')
            ->setDescription("Find missing images from official servers")
        ;
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        // example http://lcg-cdn.fantasyflightgames.com/l5r/L5C01_1.jpg
        $baseURL = "http://lcg-cdn.fantasyflightgames.com/l5r";

        $curl = new Curl();
        $curl->setOpt(CURLOPT_NOBODY, true);

        $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');

        $packCards = $entityManager->getRepository(PackCard::class)->findBy(['imageUrl' => null]);

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

        $entityManager->flush();
    }
}
