<?php

namespace AppBundle\Service;

use AppBundle\Behavior\Service\GetRepositoryTrait;
use AppBundle\Entity\Deck;
use AppBundle\Entity\DeckLike;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Description of DeckLikeManager
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DeckLikeManager
{
    use GetRepositoryTrait;

    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct (EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Add a like for this deck and user.
     * If the like was added, return the new number of likes.
     * Else return null.
     */
    public function addLike (Deck $deck, User $user): ?int
    {
        $deckLike = $this->entityManager->getRepository(DeckLike::class)->findOneBy(['deck' => $deck, 'user' => $user]);
        if ($deckLike) {
            return null;
        }
        $this->entityManager->persist(new DeckLike($deck, $user));
        $deck->setNbLikes($deck->getNbLikes() + 1);

        return $deck->getNbLikes();
    }

    /**
     * Remove a like for this deck and user.
     * If the like was removed, return the new number of likes.
     * Else return null.
     */
    public function removeLike (Deck $deck, User $user): ?int
    {
        $deckLike = $this->entityManager->getRepository(DeckLike::class)->findOneBy(['deck' => $deck, 'user' => $user]);
        if ($deckLike === null) {
            return null;
        }
        $this->entityManager->remove($deckLike);
        $deck->setNbLikes($deck->getNbLikes() - 1);

        return $deck->getNbLikes();
    }

    public function getLike (Deck $deck, User $user): ?DeckLike
    {
        return $this->entityManager->find(DeckLike::class, ['deck' => $deck, 'user' => $user]);
    }

    /** @return DeckLike[] */
    public function listLikes (Deck $deck): array
    {
        return $this->entityManager->getRepository(DeckLike::class)->findBy(['deck' => $deck]);
    }
}