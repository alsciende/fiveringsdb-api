<?php

namespace AppBundle\Command;

use AppBundle\Entity\Card;
use Cocur\Slugify\Slugify;
use function GuzzleHttp\Psr7\str;
use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Description of DataScrapeCommand
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DataParseCommand extends ContainerAwareCommand
{
    /** @var Slugify */
    protected $slugify;

    protected function configure ()
    {
        $this
            ->setName('app:data:parse')
            ->setDescription("Import data from a javascript file like the ones downloaded from cardgamedb.com")
            ->addArgument('filename', InputArgument::OPTIONAL, 'Name of the file to parse', 'l5r-db.js');
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $this->slugify = new Slugify();
        $repository = $this
            ->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository(Card::class);
        $serializer = $this
            ->getContainer()
            ->get('jms_serializer');

        $filename = $input->getArgument('filename');

        $content = file_get_contents($filename);
        $json = rtrim(substr($content, 8), ';');
        $items = json_decode($json, true);

        foreach ($items as $item) {
            dump($item);
            $normalizedItem = $this->normalizeItem($item);
            $card = $repository->find($normalizedItem['id']);

            $serializationContext = new SerializationContext();
            $serializationContext->setGroups(['Default']);
            $serializedCard = $serializer->toArray($card, $serializationContext);

            unset($serializedCard['name_canonical']);
            unset($serializedCard['text_canonical']);
            
            dump($normalizedItem);
            dump($serializedCard);
            $errors = 0;
            dump('missing in normalized');
            foreach ($serializedCard as $k => $value) {
                if (!isset($normalizedItem[$k]) || $normalizedItem[$k] != $serializedCard[$k]) {
                    dump([$k => $serializedCard[$k]]);
                    $errors++;
                }
            }
            dump('missing in serialized');
            foreach ($normalizedItem as $k => $value) {
                if (!isset($serializedCard[$k]) || $normalizedItem[$k] != $serializedCard[$k]) {
                    dump([$k => $normalizedItem[$k]]);
                    $errors++;
                }
            }
            if ($errors) {
                die;
            }
        }

        dump("no errors");
    }

    protected function normalizeItem (array $item): array
    {
        switch ($item['id']) {
            case '180':
                $item['influence'] = "3";
                break;
        }

        $normalizedItem = [];

        $type = strtolower($item['type']);
        $traits = array_map(function ($trait) {
            return trim($this->slugify->slugify($trait));
        }, array_filter(explode('.', $item['traits']), function ($trait) {
            return !empty($trait);
        }));
        $element = null;
        if ($type === 'province') {
            $element = array_shift($traits);
            $normalizedItem['element'] = $element;
        }

        foreach ($item as $key => $value) {
            switch ($key) {
                case 'altart':
                case 'ban':
                case 'fullurl':
                case 'furl':
                case 'num':
                case 'rest':
                case 'setid':
                case 'showindn':
                    break;
                case 'clan':
                case 'type':
                    $normalizedItem[$key] = strtolower($value);
                    break;
                case 'deck':
                    $deck = strtolower($value);
                    if ($deck === 'stronghold') {
                        $normalizedItem['side'] = 'province';
                    } else {
                        $normalizedItem['side'] = $deck;
                    }
                    break;
                case 'traits':
                    $normalizedItem['traits'] = $traits;
                    break;
                case 'unique':
                    $normalizedItem['unicity'] = $value === 'Y';
                    break;
                case 'text':
                    if ($value !== '') {
                        $normalizedItem[$key] = $this->transcribe($value);
                    }
                    break;
                case 'strength':
                    if ($type === 'stronghold' || $type === 'holding') {
                        $normalizedItem['strength_bonus'] = $value;
                    } else if ($value !== '') {
                        $normalizedItem['strength'] = $value;
                    }
                    break;
                case 'startinghonor':
                    if ($value !== '') {
                        $normalizedItem['honor'] = intval($value);
                    }
                    break;
                case 'influence':
                    if ($value !== '') {
                        if ($type === 'stronghold') {
                            $normalizedItem['influence_pool'] = intval($value);
                        } else {
                            $normalizedItem['influence_cost'] = intval($value);
                        }
                    } else if ($item['clan'] === 'Neutral' && $item['deck'] === 'Conflict') {
                        $normalizedItem['influence_cost'] = 0;
                    }
                    break;
                case 'max':
                    $normalizedItem['deck_limit'] = $value;
                    break;
                case 'name':
                    $normalizedItem['name'] = $this->transcribe($value);
                    break;
                case 'element':
                case 'cost':
                    if ($value !== '') {
                        $normalizedItem[$key] = $value;
                    }
                    break;
                case 'fate':
                case 'glory':
                    if ($value !== '' && $type !== 'attachment') {
                        $normalizedItem[$key] = $value;
                    }
                    break;
                case 'military':
                case 'political':
                    if ($value !== '' && $value !== '–') {
                        if ($type === 'character') {
                            $normalizedItem[$key] = $value;
                        } else {
                            $normalizedItem[$key . '_bonus'] = str_replace('–', '-', $value);
                        }
                    }
                    break;
                case 'deckrestrictions':
                    if ($value !== '' && strpos($value, 'role only') > -1) {
                        $normalizedItem['role_restriction'] = strtolower(substr($value, 0, 6));
                    }
                    break;
            }
        }

        $normalizedItem['id'] = $this->slugify->slugify($item['name']);

        ksort($normalizedItem);

        return $normalizedItem;
    }

    protected function transcribe ($text)
    {
        $text = str_replace([
            '&ndash;',
            '<em class=\'bbc\'><strong class=\'bbc\'>',
            '<strong class=\'bbc\'>',
            '</strong>:',
            '<em class=\'bbc\'>',
            '</em>',
            '</strong></i>',
            '[Military]',
            '[Politics]',
            '[Air]',
            '[Earth]',
            '[Fire]',
            '[Void]',
            '[Water]',
            '[Crab]',
            '[Crane]',
            '[Dragon]',
            '[Lion]',
            '[Phoenix]',
            '[Scorpion]',
            '[Unicorn]',
            '’',
            ']and',
            '<br /></i>',
            '<br/></i>',
            '<br />',
            '<br/>',
            '&rsquo;',
            ' </em>or<em> ',
            '.</em>.',
            '&quot;Action:',
            '&quot;',
            '&ldquo;',
            '&rdquo;',
            '<i> ',
            '(<i>',
            '󲈽',
            '<i></i>',
            ' –  ',
            'deck building',

        ], [
            '–',
            '<em>',
            '<b>',
            ':</b>',
            '<i>',
            '</i>',
            '</em>',
            '[conflict-military]',
            '[conflict-political]',
            '[element-air]',
            '[element-earth]',
            '[element-fire]',
            '[element-void]',
            '[element-water]',
            '[clan-crab]',
            '[clan-crane]',
            '[clan-dragon]',
            '[clan-lion]',
            '[clan-phoenix]',
            '[clan-scorpion]',
            '[clan-unicorn]',
            '\'',
            '] and',
            "</i>\n",
            "</i>\n",
            "\n",
            "\n",
            '\'',
            '</em> or <em>',
            '</em>.',
            '"<b>Action:</b>',
            '"',
            '"',
            '"',
            ' <i>',
            '<i>(',
            '[conflict-military]',
            '',
            ' – ',
            'deckbuilding',


        ], $text);

        return $text;
    }
}