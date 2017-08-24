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
            ->setName('app:image:fetch')
            ->setDescription("Fetch images from official servers")
            ->addArgument('folder', InputArgument::REQUIRED, "Folder where the images are")
        ;
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $imageFolder = $input->getArgument('folder');

        // example http://lcg-cdn.fantasyflightgames.com/l5r/L5C01_1.jpg
        $baseURL = "http://lcg-cdn.fantasyflightgames.com/l5r";

        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $packCards = $em->getRepository(PackCard::class)->findAll();

        foreach($packCards as $packCard) {
            $url = sprintf("%s/%s_%s.jpg",
                $baseURL,
                $packCard->getPack()->getFfgId(),
                $packCard->getPosition()
            );
            $path = sprintf("%s/%s/%s.jpg",
                $imageFolder,
                $packCard->getPack()->getId(),
                $packCard->getCard()->getId()
            );
            $this->downloadImage($url, $path);
            $output->writeln("Downloaded image for [".$packCard->getCard()->getName()."]");
        }
    }

    protected function downloadImage($url, $path)
    {
        $curl = new Curl();
        $file_handle = fopen($path, 'w+');
        $curl->setOpt(CURLOPT_FILE, $file_handle);
        $curl->get($url);
        $curl->setOpt(CURLOPT_FILE, null);
        fclose($file_handle);
    }

}
