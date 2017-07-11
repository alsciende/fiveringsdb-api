<?php

namespace AppBundle\Command;

use AppBundle\Entity\Card;
use Cocur\Slugify\Slugify;

/**
 * Description of ImportCommand
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class ImportCommand extends \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
{
    protected function configure ()
    {
        $this
            ->setName('app:import')
            ->setDescription("Import a json file with card data.")
            ->addArgument("filename", \Symfony\Component\Console\Input\InputArgument::REQUIRED, "File to import")
            ->addArgument("folder", \Symfony\Component\Console\Input\InputArgument::REQUIRED, "Folder to export to")
        ;
    }

    /** @var Slugify */
    private $slugify;

    protected function execute (\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
    {
        $this->slugify = new \Cocur\Slugify\Slugify();

        $filename = $input->getArgument('filename');
        if(!file_exists($filename)) {
            die("File does not exist");
        }

        $folder = $input->getArgument('folder');
        if(!file_exists($folder)) {
            die("Folder does not exist");
        }

        $content = file_get_contents($filename);
        $data = json_decode($content, TRUE);

        $packCards = [];

        foreach($data as $card) {
            $data = $this->import($card);
            $slug = $data['code'];

            if(!file_exists("$folder/../images/$slug.png")) {
                continue;
            }

            $position = $data['position'];
            unset($data['position']);

            $illustrator = $data['illustrator'];
            unset($data['illustrator']);

            ksort($data);
            $json = json_encode(array($data), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            $filename = "$folder/Card/$slug.json";
            file_put_contents($filename, $json);
            $output->writeln("File $filename written.");

            $packCards[] = [
                'card_code' => $slug,
                'quantity' => 1,
                'position' => $position,
                'illustrator' => $illustrator,
            ];
        }

        $json = json_encode($packCards, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $filename = "$folder/PackCard/core.json";
        file_put_contents($filename, $json);
    }

    private function import(array $data): array
    {
        $slug = $this->slugify->slugify($data['Title']);

        $keywords = empty($data['Traits']) ? [] : array_map(function ($trait) {
            return $this->slugify->slugify(str_replace('.', '', $trait));
        }, explode(' ',  $data['Traits']));

        $card = [
            'code' => $slug,
            'name' => $data['Title'],
            'cost' => $data['Cost'] ?? null,
            'text' => isset($data['Text']) ? $this->markupText($data['Text']) : null,
            'type' => strtolower($data['Type']),
            'clan' => strtolower($data['Clan']),
            'element' => isset($data['Element']) ? strtolower($data['Element']) : null,
            'unicity' => $data['Unique'] === 'Yes',
            'side' => $this->getSide($data['Deck']),
            'keywords' => $keywords,
            'illustrator' => $data['Illustrator'] ?? null,
            'military' => $this->getValue($data, 'Military Strength', 'Character'),
            'political' => $this->getValue($data, 'Political Strength', 'Character'),
            'strength' => $this->getValue($data, 'Strength', 'Province'),
            'military_bonus' => $this->getValue($data, 'Military Strength', 'Attachment'),
            'political_bonus' => $this->getValue($data, 'Political Strength', 'Attachment'),
            'strength_bonus' => $this->getValue($data, 'Strength', 'Stronghold') ?: $this->getValue($data, 'Strength', 'Holding'),
            'glory' => $data['Glory'] ?? null,
            'honor' => $data['Starting Honor'] ?? null,
            'fate' => $data['Fate Gain'] ?? null,
            'influence_pool' => $data['Influence Pool'] ?? null,
            'influence_cost' => $data['Influence Cost'] ?? ($data['Deck'] === 'Conflict' && $data['Clan'] === 'Neutral' ? 0 : null),
            'position' => intval(substr($data['Card Number'], 2, 3), 10),
            'deck_limit' => in_array($data['Type'], ['Province', 'Role', 'Stronghold']) ? 1 : 3,
        ];

        return $card;
    }

    private function getValue(array $data, string $key, string $type)
    {
        if($data['Type'] !== $type || !isset($data[$key]) || $data[$key] === '-') {
            return null;
        }
        return $data[$key];
    }

    private function getSide($deck) {
        switch($deck) {
            case 'Conflict': return 'conflict'; break;
            case 'Dynasty': return 'dynasty'; break;
            default: return null;
        }
    }

    private function markupText(string $text): string
    {
        $text = str_replace('[POL]', '[conflict-political]', $text);
        $text = str_replace('[MIL]', '[conflict-military]', $text);
        $text = str_replace('[CRANE]', '[clan-crane]', $text);
        $text = str_replace('[CRAB]', '[clan-crab]', $text);
        $text = str_replace('[DRAGON]', '[clan-dragon]', $text);
        $text = str_replace('[LION]', '[clan-lion]', $text);
        $text = str_replace('[PHOENIX]', '[clan-phoenix]', $text);
        $text = str_replace('[SCORPION]', '[clan-scorpion]', $text);
        $text = str_replace('[UNICORN]', '[clan-unicorn]', $text);
        $text = str_replace('[AIR]', '[element-air]', $text);
        $text = str_replace('[EARTH]', '[element-earth]', $text);
        $text = str_replace('[FIRE]', '[element-fire]', $text);
        $text = str_replace('[VOID]', '[element-void]', $text);
        $text = str_replace('[WATER]', '[element-water]', $text);

        return $text;
    }
}