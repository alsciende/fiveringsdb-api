<?php

namespace AppBundle\Command;

use AppBundle\Entity\Card;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Deletes a card. Takes all its links and reports them to another card.
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class ReplaceCardCommand extends Command
{
    /**
     * @var EntityManagerInterface $entityManager
     */
    private $entityManager;

    /**
     * @var EntityRepository $cardRepository
     */
    private $cardRepository;

    /**
     * @var Connection $connection
     */
    private $connection;

    /**
     * @var SymfonyStyle
     */
    private $io;

    public function __construct($name = null, EntityManagerInterface $entityManager)
    {
        parent::__construct($name);
        $this->entityManager = $entityManager;
        $this->cardRepository = $this->entityManager->getRepository(Card::class);
        $this->connection = $this->entityManager->getConnection();
    }

    protected function configure()
    {
        $this
            ->setName('app:card:replace')
            ->setDescription("Replaces a card with another")
            ->addArgument('source-card-id', InputArgument::REQUIRED, 'Id of the card that goes')
            ->addArgument('dest-card-id', InputArgument::REQUIRED, 'Id of the card that stays');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);

        $this->io->title('Replacing a card');

        $sourceId = $input->getArgument('source-card-id');
        $source = $this->cardRepository->find($sourceId);
        $destId = $input->getArgument('dest-card-id');
        $dest = $this->cardRepository->find($destId);

        if (!$source instanceof Card) {
            $this->io->error('Source card does not exist.');
            return;
        }

        if (!$dest instanceof Card) {
            $this->io->error('Dest card does not exist.');
            return;
        }

        $this->connection->beginTransaction();
        $params = [
            'source' => $sourceId,
            'dest'   => $destId,
        ];
        $this->executeUpdate('clan_roles', $params);
        $this->executeUpdate('deck_cards', $params);
        $this->connection->executeQuery('DELETE FROM pack_cards WHERE card_id=:source', [
            'source' => $sourceId,
        ]);
        $this->executeUpdate('reviews', $params);
        $this->executeUpdate('rulings', $params);
        $this->connection->executeQuery('DELETE FROM cards WHERE id=:source', [
            'source' => $sourceId,
        ]);

        $this->connection->commit();
        $this->io->success('Card replaced and deleted.');
    }

    private function executeUpdate($tableName, $params)
    {
        $affected = $this->connection->executeUpdate(
            "UPDATE $tableName SET card_id=:dest WHERE card_id=:source",
            $params
        );
        $this->io->text(sprintf('%d lines affected in %s.', $affected, $tableName));
    }
}