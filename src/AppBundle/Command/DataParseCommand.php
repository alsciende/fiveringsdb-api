<?php

namespace AppBundle\Command;

use AppBundle\Entity\Card;
use AppBundle\Entity\Pack;
use AppBundle\Entity\PackCard;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
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
    private $slugify;

    /** @var Serializer $serializer */
    private $serializer;

    /** @var string $jsonDataPath */
    private $jsonDataPath;

    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    public function __construct ($name = null, Serializer $serializer, string $jsonDataPath, EntityManagerInterface $entityManager)
    {
        parent::__construct($name);
        $this->serializer = $serializer;
        $this->jsonDataPath = $jsonDataPath;
        $this->entityManager = $entityManager;
        $this->slugify = new Slugify();
    }

    protected function configure ()
    {
        $this
            ->setName('app:data:parse')
            ->setDescription("Import data from a javascript file like the ones downloaded from cardgamedb.com")
            ->addArgument('filename', InputArgument::OPTIONAL, 'Name of the file to parse', 'l5r-db.js');
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $packCards = [];

        foreach ($this->getItemsFromFile($input->getArgument('filename')) as $item) {
            $normalizedItem = $this->normalizeItem($item);
            $packCardItem = $normalizedItem['pack_card'];
            unset($normalizedItem['pack_card']);

            $newCard = $this->serializer->fromArray($normalizedItem, Card::class);
            $serializationContext = new SerializationContext();
            $serializationContext->setGroups(['Source']);
            $serializationContext->setSerializeNull(true);
            $json = $this->serializer->serialize([$newCard], 'json', $serializationContext);

            $jsonfilename = sprintf('%s/Card/%s.json', $this->jsonDataPath, $normalizedItem['id']);
            file_put_contents($jsonfilename, $json);

            $card = $this->entityManager->find(Card::class, $normalizedItem['id']);
            $setId = $packCardItem['set_id'];
            unset($packCardItem['set_id']);
            if (!isset($packCards[$setId])) {
                $packCards[$setId] = [];
            }
            $pack = $this->entityManager->find(Pack::class, $setId);
            /** @var PackCard $packCard */
            $packCard = $this->entityManager->getRepository(PackCard::class)->findOneBy(['card' => $card, 'pack' => $pack]);
            if ($packCard instanceof PackCard) {
                if (!isset($packCardItem['flavor'])) {
                    $packCardItem['flavor'] = $packCard->getFlavor();
                }
                if (!isset($packCardItem['quantity'])) {
                    $packCardItem['quantity'] = $packCard->getQuantity();
                }
            } else {
                $packCardItem['flavor'] = null;
                $packCardItem['quantity'] = 3;
            }
            ksort($packCardItem);
            $packCards[$setId][] = $packCardItem;
        }

        foreach ($packCards as $setId => $packCardList) {
            usort($packCardList, function ($a, $b) {
                return $a <=> $b;
            });
            $serializationContext = new SerializationContext();
            $serializationContext->setGroups(['Source']);
            $serializationContext->setSerializeNull(true);
            $json = $this->serializer->serialize($packCardList, 'json', $serializationContext);

            $jsonfilename = sprintf('%s/PackCard/%s.json', $this->jsonDataPath, $setId);
            file_put_contents($jsonfilename, $json);
        }
    }

    protected function getItemsFromFile (string $filename): array
    {
        $content = file_get_contents($filename);
        $json = rtrim(substr($content, 8), ';');

        return json_decode($json, true);
    }

    protected function normalizeItem (array $item): array
    {
        switch ($item['id']) {
            case '180':
                $item['influence'] = "3";
                break;
            case '170':
                $item['text'] = 'This character gets +1[Military] and +1[Politics] for each claimed ring.';
                break;
            case '63':
                $item['text'] = 'No attachments except <em class=\'bbc\'><strong class=\'bbc\'>Monk</strong></em> or <em class=\'bbc\'><strong class=\'bbc\'>Tattoo</strong></em>.<br/><strong class=\'bbc\'>Action</strong>: While this character is attacking, spend 1 fate to an unclaimed ring. Choose a <em class=\'bbc\'><strong class=\'bbc\'>Monk</strong></em> character or a character with a <em class=\'bbc\'><strong class=\'bbc\'>Monk</strong></em> attachment &ndash; ready that character.';
                break;
            case '3':
                $item['text'] = '<strong class=\'bbc\'>Action</strong>: During a conflict, bow this stronghold. Choose a participating character with 1 or more attachments on it &ndash; until the end of the conflict, that character gets +1[Military] and +1[Politics] (+2[Military] and +2[Politics] instead if it has 2 or more attachments on it).';
                break;
            case '171':
                $item['text'] = 'While this character is attacking, the contested ring gains the [Air] element. If this character wins the conflict as an attacker, you may choose which of its ring effects to resolve.';
                break;
        }

        $normalizedItem = [
            'pack_card' => [],
        ];

        $type = strtolower($item['type']);

        $traits = array_map(function ($trait) {
            return trim($this->slugify->slugify($trait));
        }, array_filter(explode('.', $item['traits']), function ($trait) {
            return !empty($trait);
        }));

        $element = null;
        if ($type === 'province') {
            foreach ($traits as $i => $trait) {
                if (in_array($trait, ['air', 'earth', 'fire', 'void', 'water'])) {
                    unset($traits[$i]);
                    $normalizedItem['element'] = $trait;
                }
            }
            $traits = array_values($traits);
        }

        foreach ($item as $key => $value) {
            switch ($key) {
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
                    $normalizedItem['unicity'] = $value === 'Y' || $type === 'stronghold';
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
                        $normalizedItem['strength'] = intval($value);
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
                case 'img':
                    $normalizedItem['pack_card']['image_url'] = 'http://lcg-cdn.fantasyflightgames.com/l5r/' . $value;
                    break;
                case 'setname':
                    if ($value === 'Core Set') {
                        $value = 'Core';
                    }
                    $normalizedItem['pack_card']['set_id'] = $this->slugify->slugify($value);
                    break;
                case 'num':
                    $normalizedItem['pack_card']['position'] = ltrim($value, '0');
                    break;
                case 'illus':
                    $normalizedItem['pack_card']['illustrator'] = $value;
                    break;
                case 'altart':
                case 'ban':
                case 'fullurl':
                case 'furl':
                case 'rest':
                case 'setid':
                case 'showindn':
                    break;
            }
        }

        $normalizedItem['id'] = $this->slugify->slugify($item['name']);
        $normalizedItem['pack_card']['card_id'] = $normalizedItem['id'];

        ksort($normalizedItem);

        return $normalizedItem;
    }

    protected function transcribe ($text)
    {
        $text = preg_replace('/  +/', ' ', $text);
        $text = preg_replace('/(<[^<\/]+>) /', ' $1', $text);
        $text = preg_replace('/\((<[^<\/]+>)/', '$1(', $text);

        $text = str_replace([
            '&ndash;',
            '<em class=\'bbc\'><strong class=\'bbc\'>',
            '<strong class=\'bbc\'>',
            '</strong>:',
            '<em class=\'bbc\'>',
            '</em>',
            '</strong></i>',
            ' </em>',
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
            '<br />',
            '<br/></i>',
            '<br/>',
            '&rsquo;',
            '&quot;Action:',
            '&quot;',
            '&ldquo;',
            '&rdquo;',
            'deck building',
        ], [
            '–',
            '<em>',
            '<b>',
            ':</b>',
            '<i>',
            '</i>',
            '</em>',
            '</em> ',
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
            '<br/>',
            "</i>\n",
            "\n",
            '\'',
            '"<b>Action:</b>',
            '"',
            '"',
            '"',
            'deckbuilding',
        ], $text);

        return $text;
    }
}