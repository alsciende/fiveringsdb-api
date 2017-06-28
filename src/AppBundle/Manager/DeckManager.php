<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Card;
use AppBundle\Entity\Deck;
use AppBundle\Entity\DeckCard;
use AppBundle\Entity\User;
use AppBundle\Model\CardSlotCollectionDecorator;
use AppBundle\Service\DeckValidator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;

/**
 * Description of DeckManager
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DeckManager extends BaseManager
{
    /** @var DeckValidator */
    private $deckValidator;

    /** @var \AppBundle\Repository\DeckRepository */
    private $deckRepository;

    /** @var \AppBundle\Repository\CardRepository */
    private $cardRepository;

    /** @var \AppBundle\Repository\DeckCardRepository */
    private $deckCardRepository;

    public function __construct (EntityManagerInterface $entityManager, Serializer $serializer, DeckValidator $deckValidator)
    {
        $this->deckValidator = $deckValidator;
        parent::__construct($entityManager, $serializer);
        $this->deckRepository = $this->entityManager->getRepository(Deck::class);
        $this->cardRepository = $this->entityManager->getRepository(Card::class);
        $this->deckCardRepository = $this->entityManager->getRepository(DeckCard::class);
    }

    /**
     * Create a new deck from $data. It is private.
     */
    public function createNewInitialDeck (array $data, User $user): Deck
    {
        $deck = new Deck();
        $deck->setUser($user);
        $deck->setName($data['name']);
        $deck->setDescription($data['description']);
        foreach($this->denormalizeDeckCards($data['cards']) as $deckCard) {
            $deck->addDeckCard($deckCard);
        }
        $deck->setProblem($this->deckValidator->check($deck->getDeckCards()));
        $deck->setIsPublished(FALSE);
        $deck->setMajorVersion(0);
        $deck->setMinorVersion(1);
        $deck->setGenus(\Ramsey\Uuid\Uuid::uuid4());
        $deck->setLineage(\Ramsey\Uuid\Uuid::uuid4());
        $this->entityManager->persist($deck);

        return $deck;
    }

    /**
     * Create a new minor version of $parent from $data. It is private.
     */
    public function createNewMinorVersion (array $data, Deck $parent): Deck
    {
        $deck = new Deck();
        $deck->setUser($parent->getUser());
        $deck->setName($data['name']);
        $deck->setDescription($data['description']);
        foreach($this->denormalizeDeckCards($data['cards']) as $deckCard) {
            $deck->addDeckCard($deckCard);
        }
        $deck->setProblem($this->deckValidator->check($deck->getDeckCards()));
        $deck->setIsPublished(FALSE);
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
        $deck->setIsPublished(FALSE);
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
        $deck->setIsPublished(TRUE);
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
     * Update a deck from $data.
     * Throws if deck is not published.
     * Can only update name and description.
     */
    public function update (array $data, Deck $deck): Deck
    {
        if (!$deck->getIsPublished()) {
            throw new \Exception("Cannot update private deck");
        }

        $deck->setDescription($data['description']);
        $deck->setName($data['name']);
        $merged = $this->entityManager->merge($deck);

        return $merged;
    }

    /**
     * Delete a lineage
     */
    public function deleteLineage (Deck $deck)
    {
        $this->deckRepository->removeLineage($deck->getLineage(), $deck->getUser());
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
        $deckLike = $this->entityManager->getRepository(\AppBundle\Entity\DeckLike::class)->findOneBy(['deck' => $deck, 'user' => $user]);
        if ($deckLike) {
            return null;
        }
        $this->entityManager->persist(new \AppBundle\Entity\DeckLike($deck, $user));
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
        $deckLike = $this->entityManager->getRepository(\AppBundle\Entity\DeckLike::class)->findOneBy(['deck' => $deck, 'user' => $user]);
        if (!$deckLike) {
            return null;
        }
        $this->entityManager->remove($deckLike);
        $deck->setNbLikes($deck->getNbLikes() - 1);

        return $deck->getNbLikes();
    }

    public function denormalizeDeckCards(array $data): array
    {
        $deckCards = [];
        foreach($data as $card_code => $quantity) {
            $card = $this->cardRepository->find($card_code);
            if (!$card) {
                throw new \Exception("Card not found: $card_code");
            }
            $deckCards[] = new DeckCard($card, $quantity);
        }

        return $deckCards;
    }
}
