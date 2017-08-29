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
            ->setName('app:images:fetch')
            ->setDescription("Fetch missing images from official servers")
        ;
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $imageFolder = $this->getContainer()->getParameter('path_to_card_images');
        if (file_exists($imageFolder) === false || is_dir($imageFolder) === false) {
            $output->writeln('<error>Bad configuration: path_to_card_images</error>');

            return;
        }

        // example http://lcg-cdn.fantasyflightgames.com/l5r/L5C01_1.jpg
        $baseURL = "http://lcg-cdn.fantasyflightgames.com/l5r";

        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $packCards = $em->getRepository(PackCard::class)->findAll();

        /** @var PackCard $packCard */
        foreach ($packCards as $packCard) {
            $path = sprintf(
                "%s/%s/%s.jpg",
                $imageFolder,
                $packCard->getPack()->getId(),
                $packCard->getCard()->getId()
            );

            if (file_exists($path)) {
                continue;
            }

            $url = sprintf(
                "%s/%s_%s.jpg",
                $baseURL,
                $packCard->getPack()->getFfgId(),
                $packCard->getPosition()
            );
            if ($this->downloadImage($url, $path)) {
                $output->writeln(sprintf('<info>Downloaded image for %s</info>', (string)$packCard));
            } else {
                $output->writeln(sprintf('<error>Cannot downloaded image for %s</error>', (string)$packCard));
            }
        }
    }

    protected function downloadImage ($url, $path)
    {
        $curl = new Curl();
        $file_handle = fopen($path, 'w+');
        $curl->setOpt(CURLOPT_FILE, $file_handle);
        $curl->get($url);
        $curl->setOpt(CURLOPT_FILE, null);
        fclose($file_handle);

        if ($curl->error) {
            unlink($path);
        }
    }

}
