<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Deck;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

/**
 * @author Alsciende <alsciende@icloud.com>
 */
class DeckRepository extends EntityRepository
{

    /**
     * Remove all private decks of a lineage
     */
    public function removeLineage (string $lineage, User $user): void
    {
        $decks = $this->findByLineage($lineage, $user);
        foreach ($decks as $deck) {
            if (!$deck->getIsPublished()) {
                $this->getEntityManager()->remove($deck);
            }
        }
    }

    /**
     * Return the last private deck of a lineage
     */
    public function getLastMinorVersion (string $lineage, User $user): ?Deck
    {
        $decks = $this->findByLineage($lineage, $user);
        foreach ($decks as $deck) {
            if (!$deck->getIsPublished()) {
                return $deck;
            }
        }
        return null;
    }

    /**
     * Return all private decks of a lineage
     *
     * @return \AppBundle\Entity\Deck[]
     */
    public function findByLineage (string $lineage, User $user): array
    {
        return $this->findBy(['lineage' => $lineage, 'user' => $user, 'isPublished' => FALSE], ['createdAt' => 'DESC']);
    }
}
