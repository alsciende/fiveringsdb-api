<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Card;
use AppBundle\Entity\Deck;
use AppBundle\Entity\DeckCard;
use AppBundle\Entity\DeckDice;
use AppBundle\Entity\Dice;
use AppBundle\Entity\User;
use AppBundle\Service\DeckChecker;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Serializer\Serializer;

/**
 * Description of DeckManager
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DeckManager extends BaseManager
{

    /**
     *
     * @var DeckChecker
     */
    private $deckChecker;

    /**
     *
     * @var \AppBundle\Repository\DeckRepository
     */
    private $deckRepository;

    /**
     *
     * @var \AppBundle\Repository\CardRepository
     */
    private $cardRepository;

    /**
     *
     * @var \AppBundle\Repository\DeckCardRepository
     */
    private $deckCardRepository;

    public function __construct (EntityManager $entityManager, Serializer $serializer, DeckChecker $deckChecker)
    {
        $this->deckChecker = $deckChecker;
        parent::__construct($entityManager, $serializer);
        $this->deckRepository = $this->entityManager->getRepository(Deck::class);
        $this->cardRepository = $this->entityManager->getRepository(Card::class);
        $this->deckCardRepository = $this->entityManager->getRepository(DeckCard::class);
    }

    /**
     * Create a new deck from $data. It is private.
     * 
     * @param array $data
     * @param User $user
     * @return Deck
     */
    public function createNewInitialDeck (array $data, User $user)
    {
        $cards = $data['cards'];
        unset($data['cards']);
        $dices = $data['dices'];
        unset($data['dices']);
        $phoenixbornCode = $data['phoenixborn_code'];
        unset($data['phoenixborn_code']);
        /* @var $deck Deck */
        $deck = $this->serializer->denormalize($data, Deck::class);
        $this->setPhoenixborn($deck, $phoenixbornCode);
        $this->setDeckCards($deck, $cards);
        $this->setDeckDices($deck, $dices);
        $deck->setUser($user);
        $deck->setProblem($this->deckChecker->check($deck));
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
     * 
     * @param array $data
     * @param Deck $parent
     * @return type
     */
    public function createNewMinorVersion (array $data, Deck $parent)
    {
        $cards = $data['cards'];
        unset($data['cards']);
        $dices = $data['dices'];
        unset($data['dices']);
        $phoenixbornCode = $data['phoenixborn_code'];
        unset($data['phoenixborn_code']);
        /* @var $deck Deck */
        $deck = $this->serializer->denormalize($data, Deck::class);
        $this->setPhoenixborn($deck, $phoenixbornCode);
        $this->setDeckCards($deck, $cards);
        $this->setDeckDices($deck, $dices);
        $deck->setUser($parent->getUser());
        $deck->setProblem($this->deckChecker->check($deck));
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
     * 
     * @param Deck $parent
     * @param User $user
     * @return type
     */
    public function createNewCopy (Deck $parent, User $user)
    {
        /* @var $deck Deck */
        $deck = new Deck();
        $deck->setName($parent->getName());
        $deck->setDescription($parent->getDescription());
        $this->setPhoenixborn($deck, $parent->getPhoenixbornCode());
        $this->setDeckCards($deck, $parent->getCards());
        $this->setDeckDices($deck, $parent->getDices());
        $deck->setUser($user);
        $deck->setProblem($this->deckChecker->check($deck));
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
     * 
     * @param array $data
     * @param Deck $parent
     * @return type
     */
    public function createNewMajorVersion (Deck $parent)
    {
        /* @var $deck Deck */
        $deck = new Deck();
        $deck->setName($parent->getName());
        $deck->setDescription($parent->getDescription());
        $this->setPhoenixborn($deck, $parent->getPhoenixbornCode());
        $this->setDeckCards($deck, $parent->getCards());
        $this->setDeckDices($deck, $parent->getDices());
        $deck->setUser($parent->getUser());
        $deck->setProblem($this->deckChecker->check($deck));
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
     * 
     * @param array $data
     * @param Deck $deck
     */
    public function update (array $data, Deck $deck)
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
     * 
     * @param Deck $deck
     */
    public function deleteLineage (Deck $deck)
    {
        $this->deckRepository->removeLineage($deck->getLineage(), $deck->getUser());
        return TRUE;
    }

    /**
     * Delete a deck
     * 
     * @param Deck $deck
     */
    public function deleteDeck (Deck $deck)
    {
        $this->entityManager->remove($deck);
        return TRUE;
    }

    /**
     * Add a like for this deck and user.
     * If the like was added, return the new number of likes.
     * Else return FALSE.
     * 
     * @param Deck $deck
     * @param User $user
     * @return boolean
     */
    public function addLike(Deck $deck, User $user)
    {
        $deckLike = $this->entityManager->getRepository(\AppBundle\Entity\DeckLike::class)->findOneBy(['deck' => $deck, 'user' => $user]);
        if($deckLike) {
            return FALSE;
        }
        $this->entityManager->persist(new \AppBundle\Entity\DeckLike($deck, $user));
        $deck->setNbLikes($deck->getNbLikes() + 1);
        return $deck->getNbLikes();
    }
    
    /**
     * Remove a like for this deck and user.
     * If the like was removed, return the new number of likes.
     * Else return FALSE.
     * 
     * @param Deck $deck
     * @param User $user
     * @return boolean
     */
    public function removeLike(Deck $deck, User $user)
    {
        $deckLike = $this->entityManager->getRepository(\AppBundle\Entity\DeckLike::class)->findOneBy(['deck' => $deck, 'user' => $user]);
        if(!$deckLike) {
            return FALSE;
        }
        $this->entityManager->remove($deckLike);
        $deck->setNbLikes($deck->getNbLikes() - 1);
        return $deck->getNbLikes();
    }
    
    public function setPhoenixborn (Deck $deck, $phoenixbornCode)
    {
        $phoenixborn = $this->entityManager->getRepository(Card::class)->find($phoenixbornCode);
        $deck->setPhoenixborn($phoenixborn);
    }

    public function setDeckCards (Deck $deck, array $data)
    {
        $deck->clearDeckCards();

        foreach ($data as $card_code => $quantity) {
            $card = $this->cardRepository->find($card_code);
            if (!$card) {
                throw new \Exception("Card not found: $card_code");
            }
            $this->setDeckCard($deck, $card, $quantity);
        }
    }

    public function setDeckCard (Deck $deck, Card $card, int $quantity)
    {
        $deckCard = $this->deckCardRepository->findOneBy(['card' => $card, 'deck' => $deck]);
        if (!$deckCard) {
            $deckCard = new DeckCard($deck, $card, $quantity);
        } else {
            $deckCard->setQuantity($quantity);
        }

        $deck->addDeckCard($deckCard);
    }

    public function setDeckDices (Deck $deck, array $data)
    {
        $deck->clearDeckDices();

        foreach ($data as $dice_code => $quantity) {
            $dice = $this->diceRepository->find($dice_code);
            if (!$dice) {
                throw new \Exception("Dice not found: $dice_code");
            }
            $this->setDeckDice($deck, $dice, $quantity);
        }
    }

    public function setDeckDice (Deck $deck, Dice $dice, int $quantity)
    {
        $deckDice = $this->deckDiceRepository->findOneBy(['dice' => $dice, 'deck' => $deck]);
        if (!$deckDice) {
            $deckDice = new DeckDice($deck, $dice, $quantity);
        } else {
            $deckDice->setQuantity($quantity);
        }

        $deck->addDeckDice($deckDice);
    }

}
