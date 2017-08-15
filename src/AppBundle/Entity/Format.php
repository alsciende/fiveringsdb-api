<?php

namespace AppBundle\Entity;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * Deckbuilding Format of a Deck
 *
 * @ORM\Table(name="formats")
 * @ORM\Entity()
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Format
{
    use TimestampableEntity;

    /**
     * Name of the format
     *
     * @var string
     * @ORM\Column(name="id", type="string", length=255, nullable=false, unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * Simple-text description of the format
     *
     * @var string
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    private $description;

    public function getId (): string
    {
        return $this->id;
    }

    public function setId (string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getDescription (): string
    {
        return $this->description;
    }

    public function setDescription (string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @var Collection|Deck[]
     * @ORM\OneToMany(targetEntity="Deck", mappedBy="format")
     */
    private $decks;

    /** @param Collection|Deck[] $decks */
    public function setDecks (Collection $decks): self
    {
        $this->clearDecks();
        foreach ($decks as $deck) {
            $this->addDeck($deck);
        }

        return $this;
    }

    public function addDeck (Deck $deck): self
    {
        if ($this->decks->contains($deck) === false) {
            $this->decks->add($deck);
            $deck->setFormat($this);
        }

        return $this;
    }

    /** @return Collection|Deck[] */
    public function getDecks (): Collection
    {
        return $this->decks;
    }

    public function removeDeck (Deck $deck): self
    {
        if ($this->decks->contains($deck)) {
            $this->decks->removeElement($deck);
            $deck->setFormat(null);
        }

        return $this;
    }

    public function clearDecks (): self
    {
        foreach ($this->getDecks() as $deck) {
            $this->removeDeck($deck);
        }
        $this->decks->clear();

        return $this;
    }
}