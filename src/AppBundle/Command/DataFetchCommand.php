<?php

namespace AppBundle\Command;

use AppBundle\Entity\Card;
use AppBundle\Entity\Pack;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

/**
 * Description of DataFetchCommand
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DataFetchCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ObjectRepository
     */
    private $cardRepository;

    /**
     * @var string
     */
    private $imageBaseURL;

    /**
     * @var ObjectRepository
     */
    private $packRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct($name = null, EntityManagerInterface $entityManager)
    {
        parent::__construct($name);
        $this->entityManager = $entityManager;
        $this->cardRepository = $this->entityManager->getRepository(Card::class);
        $this->packRepository = $this->entityManager->getRepository(Pack::class);
        $this->imageBaseURL = "http://lcg-cdn.fantasyflightgames.com/l5r/";
    }

    protected function configure()
    {
        $this
            ->setName('app:data:fetch')
            ->setDescription("Fetch a data file for a pack from cardgamedb.com")
            ->addArgument('pack-id', InputArgument::OPTIONAL, 'Id of the pack to fetch');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->logger = new ConsoleLogger($output);

        $id = $input->getArgument('pack-id') ? $input->getArgument('pack-id') : $this->askPackId($input, $output);
        $pack = $this->entityManager->find(Pack::class, $id);
        if (!$pack instanceof Pack) {
            throw new \RuntimeException('Unknown pack id');
        }

        $this->writeFile($output, $id, array_map(
            [$this, 'getPackCard'],
            $this->filterByPack(
                $this->getItemsFromFile(
                    $this->getFileUrl($pack)
                ),
                $pack
            )
        ));
    }

    /**
     * @param string $filename
     * @return array
     */
    protected function getItemsFromFile(string $filename): array
    {
        $content = file_get_contents($filename);
        $json = rtrim(substr($content, 8), ';');

        return json_decode($json, true);
    }

    /**
     * @param array $items
     * @param Pack  $pack
     * @return array
     */
    private function filterByPack(array $items, Pack $pack)
    {
        return array_filter($items, function ($item) use ($pack) {
            return $item['setname'] === $pack->getName();
        });
    }

    /**
     * @param string $name
     * @return Card
     */
    protected function getCard(string $name): Card
    {
        $result = $this->cardRepository->findBy(['name' => $name]);

        if (count($result) < 1) {
            $this->logger->critical(sprintf(
                'Unknown card with name %s. Aborting.',
                $name
            ));
            die;
        }

        if (count($result) > 1) {
            $this->logger->alert(sprintf(
                'More than one card with name %s, edit the resulting file manually.',
                $name
            ));
        }

        return $result[0];
    }

    /**
     * @param array $item
     * @return array
     */
    protected function getPackCard(array $item): array
    {
        return [
            "card_id"     => $this->getCard(str_replace("’", "'", $item['name']))->getId(),
            "flavor"      => "",
            "illustrator" => $item['illus'],
            "image_url"   => $this->imageBaseURL . $item['img'],
            "position"    => ltrim($item['num'], 0),
            "quantity"    => 3,
        ];
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return mixed
     */
    protected function askPackId(InputInterface $input, OutputInterface $output)
    {
        $packs = $this->packRepository->findBy([], ['ffgId' => 'ASC']);
        $arrayMap = array_map(
            function (Pack $pack) {
                return $pack->getId();
            },
            $packs
        );
        $choiceQuestion = new ChoiceQuestion(
            'Pack: ',
            $arrayMap
        );
        $id = $this->getHelper('question')->ask($input, $output, $choiceQuestion);

        return $id;
    }

    /**
     * @param OutputInterface $output
     * @param string          $packId
     * @param array           $packCards
     */
    protected function writeFile(OutputInterface $output, string $packId, array $packCards): void
    {
        usort($packCards, function ($a, $b) {
            return $a['position'] <=> $b['position'];
        });

        $filename = sprintf('%s.json', $packId);
        file_put_contents($filename, json_encode($packCards, JSON_PRETTY_PRINT));
        $output->writeln(sprintf('Wrote file %s with %d cards', $filename, count($packCards)));
    }

    /**
     * @param Pack $pack
     * @return string
     */
    protected function getFileUrl(Pack $pack): string
    {
        return sprintf(
            'http://www.cardgamedb.com/deckbuilders/legendofthefiverings/database/%s-db.jgz',
            $pack->getFfgId()
        );
    }

}
