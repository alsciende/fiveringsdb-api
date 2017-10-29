<?php
/**
 * Created by PhpStorm.
 * User: cedric
 * Date: 27/10/17
 * Time: 14:39
 */

namespace AppBundle\Command;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Deck;
use AppBundle\Entity\DeckLike;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 */
class UpdateFreshnessCommand extends ContainerAwareCommand
{
    protected function configure ()
    {
        $this
            ->setName('app:freshness:update')
            ->setDescription("Update freshness in database");
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $this->update(Deck::class, 'publishedAt');
        $this->update(DeckLike::class);
        $this->update(Comment::class);
    }

    private function update (string $entityName, string $dateField = 'createdAt')
    {
        $this->updateIfOlderThan($entityName, $dateField, 1, 1);
        $this->updateIfOlderThan($entityName, $dateField, 7, 2);
        $this->updateIfOlderThan($entityName, $dateField, 30, 3);
    }

    private function updateIfOlderThan (string $entityName, string $dateField, int $days, int $freshness)
    {
        $old = $freshness - 1;
        $dql = "UPDATE $entityName e 
        SET e.freshness = $freshness 
        WHERE e.freshness = $old
        AND DATEDIFF(NOW(), e.$dateField) > :days";
        $query = $this->getContainer()
                      ->get('doctrine.orm.entity_manager')
                      ->createQuery($dql)
                      ->setParameter('days', $days);
        $query->execute();
    }
}