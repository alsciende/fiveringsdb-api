<?php

namespace AppBundle\Service\DeckSearch;

use AppBundle\Entity\Deck;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Description of AbstractDeckSearch
 *
 * @author Alsciende <alsciende@icloud.com>
 */
abstract class AbstractDeckSearchService implements DeckSearchServiceInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct (EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function getEntityManager ()
    {
        return $this->entityManager;
    }

    protected function getDeckRepository ()
    {
        return $this->entityManager->getRepository(Deck::class);
    }

    protected function getTotal (): int
    {
        $dql = "SELECT COUNT(d)
        FROM AppBundle:Deck d 
        WHERE d.published=:published 
        ORDER BY d.createdAt DESC";
        $query = $this->getEntityManager()
                      ->createQuery($dql)
                      ->setParameter('published', true);

        return (int) $query->getSingleScalarResult();
    }
}
