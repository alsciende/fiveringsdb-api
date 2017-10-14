<?php

namespace AppBundle\Command;

use AppBundle\Entity\Deck;
use AppBundle\Entity\Feature;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Description of VotesElectCommand
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class VotesElectCommand extends ContainerAwareCommand
{
    protected function configure ()
    {
        $this
            ->setName('app:votes:elect')
            ->setDescription("Elect the winning decks on a given period")
            ->addArgument('period', InputArgument::REQUIRED, 'Number of days of period');
    }

    /**
     * @return EntityManagerInterface
     */
    private function getEntityManager ()
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $period = $input->getArgument('period');
        $today = new \DateTime();

        foreach ($this->findClans() as $clan) {
            $deck = $this->findBestDeckForClan($clan, $period);
            if ($deck instanceof Deck) {
                $this->getEntityManager()->persist(new Feature($clan, $today, $deck));
            }
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @param string $clan
     * @param int $period
     * @return Deck|null
     */
    protected function findBestDeckForClan (string $clan, int $period): ?Deck
    {
        $dql = "SELECT d, COUNT(DISTINCT l.user) nbLikes
        FROM AppBundle:Deck d 
        LEFT JOIN d.deckLikes l
        WHERE d.published=:published 
        AND DATEDIFF(NOW(), d.createdAt) <= :days
        AND d.primaryClan=:clan
        GROUP BY d
        ORDER BY nbLikes DESC, d.createdAt DESC";
        $query = $this->getEntityManager()
                      ->createQuery($dql)
                      ->setParameter('published', true)
                      ->setParameter('days', $period)
                      ->setParameter('clan', $clan)
                      ->setFirstResult(0)
                      ->setMaxResults(1);

        $result = $query->getOneOrNullResult();

        if ($result === null) {
            return null;
        }

        return $result[0];
    }

    /**
     * @return string[]
     */
    protected function findClans ()
    {
        return array_map(function ($item) {
            return $item['primaryClan'];
        }, $this
            ->getEntityManager()
            ->createQuery("SELECT DISTINCT d.primaryClan FROM AppBundle:Deck d")
            ->getArrayResult()
        );
    }
}