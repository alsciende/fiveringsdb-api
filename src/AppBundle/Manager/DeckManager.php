<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Card;
use AppBundle\Entity\Deck;
use AppBundle\Entity\DeckCard;
use AppBundle\Entity\DeckLike;
use AppBundle\Entity\User;
use AppBundle\Exception\CardNotFoundException;
use AppBundle\Model\CardSlotCollectionDecorator;
use AppBundle\Model\CardSlotInterface;
use AppBundle\Service\DeckValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;

/**
 * Description of DeckManager
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DeckManager
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var Serializer */
    protected $serializer;

    /** @var DeckValidator */
    private $deckValidator;

    public function __construct (EntityManagerInterface $entityManager, Serializer $serializer, DeckValidator $deckValidator)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->deckValidator = $deckValidator;
    }

    /**
     * Create a new deck from $data. It is private.
     */
    public function createNewInitialDeck (Deck $deck, User $user): Deck
    {
        $deck->setUser($user);
        $deck->setProblem($this->deckValidator->check($deck->getDeckCards()));
        $this->entityManager->persist($deck);

        return $deck;
    }

    /**
     * Create a new minor version of $parent from $data. It is private.
     */
    public function createNewMinorVersion (Deck $deck, Deck $parent): Deck
    {
        $deck->setUser($parent->getUser());
        $deck->setProblem($this->deckValidator->check($deck->getDeckCards()));
        $deck->setPublished(FALSE);
        $deck->setMajorVersion($parent->getMajorVersion());
        $deck->setMinorVersion($parent->getMinorVersion() + 1);
        $deck->setGenus($parent->getGenus());
        $deck->setLineage($parent->getLineage());
        $this->entityManager->persist($deck);

        return $deck;
    }

    /**
     * Create a new copy of $parent. It is private.
     */
    public function createNewCopy (Deck $parent, User $user): Deck
    {
        /* @var $deck Deck */
        $deck = new Deck();
        $deck->setUser($user);
        $deck->setName($parent->getName());
        $deck->setDescription($parent->getDescription());
        foreach($parent->getDeckCards() as $deckCard) {
            $deck->addDeckCard($deckCard);
        }
        $deck->setProblem($this->deckValidator->check($deck->getDeckCards()));
        $deck->setPublished(FALSE);
        $deck->setMajorVersion(0);
        $deck->setMinorVersion(1);
        $deck->setGenus($parent->getGenus());
        $deck->setLineage(\Ramsey\Uuid\Uuid::uuid4());
        $this->entityManager->persist($deck);

        return $deck;
    }

    /**
     * Create a new major version of $parent. It is public.
     */
    public function createNewMajorVersion (Deck $parent): Deck
    {
        /* @var $deck Deck */
        $deck = new Deck();
        $deck->setUser($parent->getUser());
        $deck->setName($parent->getName());
        $deck->setDescription($parent->getDescription());
        foreach($parent->getDeckCards() as $deckCard) {
            $deck->addDeckCard($deckCard);
        }
        $deck->setProblem($this->deckValidator->check($deck->getDeckCards()));
        $deck->setPublished(TRUE);
        $deck->setMajorVersion($parent->getMajorVersion() + 1);
        $deck->setMinorVersion(0);
        $deck->setGenus($parent->getGenus());
        $deck->setLineage($parent->getLineage());
        $this->entityManager->persist($deck);

        // the parent's version changes
        $parent->setMajorVersion($deck->getMajorVersion());
        $parent->setMinorVersion(1);

        return $deck;
    }

    /**
     * Delete a lineage
     */
    public function deleteLineage (Deck $deck)
    {
        $this->entityManager->getRepository(Deck::class)->removeLineage($deck->getLineage(), $deck->getUser());
    }

    /**
     * Delete a deck
     */
    public function deleteDeck (Deck $deck)
    {
        $this->entityManager->remove($deck);
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
}
