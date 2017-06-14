<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

/**
 * Description of DeckRepository
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DeckRepository extends EntityRepository
{

    /**
     * Remove all private decks of a lineage
     * 
     * @param string $lineage
     */
    public function removeLineage (string $lineage, User $user)
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
     * @param string $lineage
     * @return type
     */
    public function getLastMinorVersion (string $lineage, User $user)
    {
        $decks = $this->findByLineage($lineage, $user);
        foreach ($decks as $deck) {
            if (!$deck->getIsPublished()) {
                return $deck;
            }
        }
    }

    /**
     * Return all private decks of a lineage
     * 
     * @param string $lineage
     * @return \AppBundle\Entity\Deck[]
     */
    public function findByLineage (string $lineage, User $user)
    {
        return $this->findBy(['lineage' => $lineage, 'user' => $user, 'isPublished' => FALSE], ['createdAt' => 'DESC']);
    }

}
