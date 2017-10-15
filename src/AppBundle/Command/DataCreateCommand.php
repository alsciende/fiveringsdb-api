<?php
/**
 * Created by PhpStorm.
 * User: cedric
 * Date: 29/08/17
 * Time: 14:42
 */

namespace AppBundle\Command;

use AppBundle\Entity\Card;
use AppBundle\Entity\Pack;
use Cocur\Slugify\Slugify;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

/**
 */
class DataCreateCommand extends ContainerAwareCommand
{

    protected function configure ()
    {
        $this
            ->setName('app:data:new')
            ->setDescription("Create a JSON file for a card")
        ;
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $pack = $this->getPack($input, $output);

        $dir = $this->getContainer()->getParameter('path_to_json_data');

        $data = $this->getCardData($input, $output);

        $target = sprintf(
            '%s/Card/%s.json',
            $dir,
            $data['id']
        );

        if (file_exists($target) === false) {
            file_put_contents($target, json_encode([$data], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        }

        $output->writeln(sprintf('<info>Successfully created %s. Id: %s</info>', $target, $data['id']));

        $packFilePath = sprintf('%s/PackCard/%s.json', $dir, $pack->getId());
        if (file_exists($packFilePath) === false) {
            $packFile = [];
        } else {
            $packFile = json_decode(file_get_contents($packFilePath), true);
        }

        $packFile[] = [
            'card_id'     => $data['id'],
            'quantity'    => 3,
            'position'    => $this->getPosition($input, $output),
            'illustrator' => null,
            'flavor'      => null,
        ];

        usort(
            $packFile, function ($a, $b) {
            return $a['card_id'] <=> $b['card_id'];
        }
        );

        file_put_contents($packFilePath, json_encode($packFile, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        $output->writeln(sprintf('<info>Successfully updated %s</info>', $packFilePath));
    }

    protected function getPosition (InputInterface $input, OutputInterface $output): string
    {
        $helper = $this->getHelper('question');
        $position = $helper->ask($input, $output, new Question('Position: '));

        return $position;
    }

    protected function getPack (InputInterface $input, OutputInterface $output): Pack
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $helper = $this->getHelper('question');
        $packs = $em->getRepository(Pack::class)->findAll();
        $packName = $helper->ask(
            $input, $output, new ChoiceQuestion(
            'Pack: ',
            array_map(
                function (Pack $pack) {
                    return $pack->getName();
                }, $packs
            )
        )
        );
        $pack = $em->getRepository(Pack::class)->findOneBy(['name' => $packName]) or die('Cannot find Pack');

        return $pack;
    }

    protected function getCardData (InputInterface $input, OutputInterface $output): array
    {
        $helper = $this->getHelper('question');
        $slugify = new Slugify();
        $normalizer = $this->getContainer()->get('alsciende_serializer.deserializer');

        $clan = $helper->ask(
            $input, $output, new ChoiceQuestion(
            'Clan: ',
            [
                Card::CLAN_CRAB,
                Card::CLAN_CRANE,
                Card::CLAN_DRAGON,
                Card::CLAN_LION,
                Card::CLAN_NEUTRAL,
                Card::CLAN_PHOENIX,
                Card::CLAN_SCORPION,
                Card::CLAN_UNICORN,
            ]
        )
        );

        $type = $helper->ask(
            $input, $output, new ChoiceQuestion(
            'Type: ',
            [
                Card::TYPE_ATTACHMENT,
                Card::TYPE_CHARACTER,
                Card::TYPE_EVENT,
                Card::TYPE_HOLDING,
                Card::TYPE_PROVINCE,
                Card::TYPE_ROLE,
                Card::TYPE_STRONGHOLD,
            ]
        )
        );

        $element = null;
        if ($type === Card::TYPE_PROVINCE) {
            $element = $helper->ask(
                $input, $output, new ChoiceQuestion(
                'Element: ',
                [
                    Card::ELEMENT_AIR,
                    Card::ELEMENT_EARTH,
                    Card::ELEMENT_FIRE,
                    Card::ELEMENT_VOID,
                    Card::ELEMENT_WATER,
                ]
            )
            );
        }

        $side = null;
        if ($type === Card::TYPE_CHARACTER) {
            $side = $helper->ask(
                $input, $output, new ChoiceQuestion(
                'Side: ',
                [
                    Card::SIDE_CONFLICT,
                    Card::SIDE_DYNASTY,
                ]
            )
            );
        } else {
            switch ($type) {
                case Card::TYPE_HOLDING:
                    $side = Card::SIDE_DYNASTY;
                    break;
                case Card::TYPE_EVENT:
                case Card::TYPE_ATTACHMENT:
                    $side = Card::SIDE_CONFLICT;
                    break;
            }
        }

        $name = $helper->ask($input, $output, new Question('Name of the card: '));
        $id = $slugify->slugify($name);

        $card = new Card();
        $card->setId($id);
        $card->setName($name);
        $card->setSide($side);
        $card->setElement($element);
        $card->setType($type);
        $card->setClan($clan);

        $data = $normalizer->deserialize($card);
        ksort($data);

        return $data;
    }

}
