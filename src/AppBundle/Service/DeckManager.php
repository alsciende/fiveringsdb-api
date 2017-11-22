<?php

namespace AppBundle\Service;

use AppBundle\Behavior\Service\GetRepositoryTrait;
use AppBundle\Entity\Deck;

use AppBundle\Entity\Strain;
use AppBundle\Entity\User;
use AppBundle\Repository\DeckRepository;
use AppBundle\Repository\StrainRepository;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Description of DeckManager
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DeckManager
{
    use GetRepositoryTrait;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var DeckRepository */
    private $deckRepository;

    /** @var StrainRepository */
    private $strainRepository;

    /** @var DeckValidator */
    private $deckValidator;

    public function __construct (
        EntityManagerInterface $entityManager,
        DeckValidator $deckValidator
    )
    {
        $this->entityManager = $entityManager;
        $this->deckRepository = $this->getRepository($entityManager, Deck::class);
        $this->strainRepository = $this->getRepository($entityManager, Strain::class);
        $this->deckValidator = $deckValidator;
    }

    public function createNewStrain (User $user): Strain
    {
        // @TODO: quota
        $strain = new Strain($user);
        $this->entityManager->persist($strain);

        return $strain;
    }

    public function deleteExpiredDecks (Strain $strain)
    {
        $decks = array_slice(array_reverse($strain->getDecks()->toArray()), $strain->getUser()->getStrainSizeLimit());
        foreach ($decks as $deck) {
            $this->deleteDeck($deck);
        }
    }

    public function persist (Deck $deck): self
    {
        $strain = $deck->getStrain();
        if ($strain instanceof Strain) {
            $head = $strain->getHead();
            $deck->setPrimaryClan($deck->getDeckCards()->findPrimaryClan());
            $deck->setSecondaryClan($deck->getDeckCards()->findSecondaryClan($deck->getPrimaryClan()));
            $deck->setProblem($this->deckValidator->check($deck->getDeckCards(), $deck->getFormat()));
            $deck->setPublished(false);
            $deck->setPublishedAt(null);
            $deck->setMajorVersion($head === null ? 0 : $head->getMajorVersion());
            $deck->setMinorVersion($head === null ? 1 : $head->getMinorVersion() + 1);
            $this->entityManager->persist($deck);

            $strain->addDeck($deck)->setHead($deck);
            $this->deleteExpiredDecks($strain);
        }

        return $this;
    }

    public function copy (Deck $deck, Deck $parent): self
    {
        $deck->setName("Copy of " . $parent->getName());
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
        $deck->setPublishedAt(new \DateTime());
        $deck->setMajorVersion($deck->getMajorVersion() + 1);
        $deck->setMinorVersion(0);

        return $this;
    }

    /**
     * Delete a deck
     */
    public function deleteDeck (Deck $deck): bool
    {
        if ($deck->isPublished() === true && $deck->getStrain() instanceof Strain) {
            return false;
        }

        $this->entityManager->remove($deck);

        return true;
    }

    public function countDecks (User $user): int
    {
        return $this->deckRepository->countBy(['user' => $user, 'published' => false]);
    }

    public function countStrains (User $user): int
    {
        return $this->strainRepository->countBy(['user' => $user]);
    }
}
