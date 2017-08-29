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
class ImageLinkCommand extends ContainerAwareCommand
{

    protected function configure ()
    {
        $this
            ->setName('app:images:link')
            ->setDescription("Install images")
        ;
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $sourceFolder = $this->getContainer()->getParameter('path_to_card_images');
        if (file_exists($sourceFolder) === false || is_dir($sourceFolder) === false) {
            $output->writeln('<error>Bad configuration: path_to_card_images</error>');
            return;
        }

        $targetFolder = $this->getContainer()->get('kernel')->getProjectDir() . '/vue/static/cards';

        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $packCards = $em->getRepository(PackCard::class)->findAll();

        /** @var PackCard $packCard */
        foreach($packCards as $packCard) {
            $source = sprintf("%s/%s/%s.jpg",
                $sourceFolder,
                $packCard->getPack()->getId(),
                $packCard->getCard()->getId()
            );

            if(file_exists($source) === false) {
                $output->writeln(sprintf('<comment>Image missing for %s</comment>', (string) $packCard));
                continue;
            }

            $dir = sprintf('%s/%s',
                $targetFolder,
                $packCard->getPack()->getId()
            );

            if(file_exists($dir) === false) {
                mkdir($dir) or die('Cannot create folder: '.$dir);
            }

            $target = sprintf('%s/%s.jpg',
                $dir,
                $packCard->getCard()->getId()
            );

            if(file_exists($target) === false) {
                link($source, $target);
            }
        }
    }
}
