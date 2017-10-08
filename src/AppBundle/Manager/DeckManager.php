<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Deck;
use AppBundle\Entity\DeckLike;
use AppBundle\Entity\Strain;
use AppBundle\Entity\User;
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

    public function createNewStrain (User $user): Strain
    {
        // @TODO: quota
        $strain = new Strain($user);
        $this->entityManager->persist($strain);

        return $strain;
    }

    public function persist (Deck $deck): self
    {
        $head = $deck->getStrain()->getHead();
        $deck->setPrimaryClan($deck->getDeckCards()->findPrimaryClan());
        $deck->setSecondaryClan($deck->getDeckCards()->findSecondaryClan($deck->getPrimaryClan()));
        $deck->setProblem($this->deckValidator->check($deck->getDeckCards(), $deck->getFormat()));
        $deck->setPublished(false);
        $deck->setMajorVersion($head === null ? 0 : $head->getMajorVersion());
        $deck->setMinorVersion($head === null ? 1 : $head->getMinorVersion() + 1);
        $this->entityManager->persist($deck);

        $deck->getStrain()->setHead($deck);

        return $this;
    }

    public function copy (Deck $deck, Deck $parent): self
    {
        $deck->setName($parent->getName());
        $deck->setDescription($parent->getDescription());
        $deck->setFormat($parent->getFormat());
        foreach ($parent->getDeckCards() as $deckCard) {
            $deck->addDeckCard($deckCard);
        }

        return $this;
    }

    /**
     * Set $deck as a major version. It becomes public.
     */
    public function publish (Deck $deck): self
    {
        $deck->setPublished(true);
        $deck->setMajorVersion($deck->getMajorVersion() + 1);
        $deck->setMinorVersion(0);

        return $this;
    }

    /**
     * Delete a deck
     */
    public function deleteDeck (Deck $deck)
    {
        $this->entityManager->remove($deck);
    }

    public function countDecks (User $user)
    {
        return $this->entityManager->getRepository(Deck::class)->countBy(['user' => $user, 'published' => false]);
    }

    public function countStrains (User $user)
    {
        return $this->entityManager->getRepository(Strain::class)->countBy(['user' => $user]);
    }
}
