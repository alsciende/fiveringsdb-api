<?php

namespace AppBundle\Entity;

use AppBundle\Model\CardSlotCollectionDecorator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * A Deck, private (minorVersion > 0) or public (minorVersion == 0)
 *
 * @ORM\Table(name="decks")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DeckRepository")
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Deck
{
    const FORMAT_STANDARD = 'standard';
    const FORMAT_SINGLE_CORE = 'single-core';

    use TimestampableEntity;

    /**
     * Unique identifier of the deck
     *
     * @var string
     * @ORM\Column(name="id", type="string", length=255, unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * Name of the deck
     *
     * @var string
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * Markdown-formatted description of the deck
     *
     * @var string
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * The cards used by the deck
     *
     * @var Collection
     * @ORM\OneToMany(targetEntity="DeckCard", mappedBy="deck", cascade={"persist", "remove", "merge"})
     */
    private $deckCards;

    /**
     * The owner of the deck
     *
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * The strain of the deck
     *
     * @var Strain
     * @ORM\ManyToOne(targetEntity="Strain", inversedBy="decks")
     * @ORM\JoinColumn(name="strain_id", referencedColumnName="id", nullable=false)
     */
    private $strain;

    /**
     * The number of likes, for a public deck
     *
     * @var integer
     * @ORM\Column(name="nb_likes", type="integer", nullable=true)
     */
    private $nbLikes;

    /**
     * Comments on the deck
     *
     * @var Collection
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="deck", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    private $comments;

    /**
     * The major version of the deck. Incremented with each publication.
     *
     * @var integer
     * @ORM\Column(name="major_version", type="integer", nullable=false)
     */
    private $majorVersion;

    /**
     * The minor version of the deck. 0 for a public deck. Incremented with each save.
     *
     * @var integer
     * @ORM\Column(name="minor_version", type="integer", nullable=false)
     */
    private $minorVersion;

    /**
     * Whether the deck is published
     *
     * @var boolean
     * @ORM\Column(name="published", type="boolean", nullable=false)
     */
    private $published;

    /**
     * A number indicating a problem with the deck
     *
     * @var integer
     * @ORM\Column(name="problem", type="integer", nullable=false)
     */
    private $problem;

    /**
     * Main Clan of the deck
     *
     * @var string
     * @ORM\Column(name="clan", type="string", nullable=true)
     */
    private $clan;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    protected $format;

    function __construct ()
    {
        $this->nbLikes = 0;
        $this->majorVersion = 0;
        $this->minorVersion = 1;
        $this->published = false;
        $this->createdAt = new \DateTime();
        $this->deckCards = new ArrayCollection();
    }

    function __toString ()
    {
        return sprintf("%s (%s)", $this->name, $this->id ?: "no id");
    }

    function getId (): ?string
    {
        return $this->id;
    }

    function setId (string $id): self
    {
        $this->id = $id;

        return $this;
    }

    function getName (): ?string
    {
        return $this->name;
    }

    function setName (string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setDeckCards (Collection $deckCards): self
    {
        $this->clearDeckCards();
        foreach ($deckCards as $deckCard) {
            $this->addDeckCard($deckCard);
        }

        return $this;
    }

    public function addDeckCard (DeckCard $deckCard): self
    {
        if ($this->deckCards->contains($deckCard) === false) {
            $this->deckCards->add($deckCard);
            $deckCard->setDeck($this);
        }

        return $this;
    }

    /** @return Collection|DeckCard[] */
    public function getDeckCards (): CardSlotCollectionDecorator
    {
        return new CardSlotCollectionDecorator($this->deckCards->toArray());
    }

    public function removeDeckCard (DeckCard $deckCard): self
    {
        if ($this->deckCards->contains($deckCard)) {
            $this->deckCards->removeElement($deckCard);
            $deckCard->setDeck(null);
        }

        return $this;
    }

    public function clearDeckCards (): self
    {
        foreach ($this->getDeckCards() as $deckCard) {
            $this->removeDeckCard($deckCard);
        }
        $this->deckCards->clear();

        return $this;
    }

    function getDeck (): ?Deck
    {
        return $this->deck;
    }

    function setDeck (Deck $deck): self
    {
        $this->deck = $deck;

        return $this;
    }

    function getUser (): ?User
    {
        return $this->user;
    }

    function setUser (User $user): self
    {
        $this->user = $user;

        return $this;
    }

    function getStrain (): ?Strain
    {
        return $this->strain;
    }

    function setStrain (Strain $strain): self
    {
        $this->strain = $strain;

        return $this;
    }

    function getNbLikes (): int
    {
        return $this->nbLikes;
    }

    function setNbLikes (int $nbLikes): self
    {
        $this->nbLikes = $nbLikes;

        return $this;
    }

    public function setComments (Collection $comments): self
    {
        $this->clearComments();
        foreach ($comments as $comment) {
            $this->addComment($comment);
        }

        return $this;
    }

    public function addComment (Comment $comment): self
    {
        if ($this->comments->contains($comment) === false) {
            $this->comments->add($comment);
            $comment->setDeck($this);
        }

        return $this;
    }

    /** @return Collection|Comment[] */
    public function getComments (): Collection
    {
        return $this->comments;
    }

    public function removeComment (Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            $comment->setDeck(null);
        }

        return $this;
    }

    public function clearComments (): self
    {
        foreach ($this->getComments() as $comment) {
            $this->removeComment($comment);
        }
        $this->comments->clear();

        return $this;
    }

    function getDescription (): ?string
    {
        return $this->description;
    }

    function setDescription (string $description = null): self
    {
        $this->description = $description;

        return $this;
    }

    function getMajorVersion (): int
    {
        return $this->majorVersion;
    }

    function getMinorVersion (): int
    {
        return $this->minorVersion;
    }

    function setMajorVersion (int $majorVersion): self
    {
        $this->majorVersion = $majorVersion;

        return $this;
    }

    function setMinorVersion (int $minorVersion): self
    {
        $this->minorVersion = $minorVersion;

        return $this;
    }

    function isPublished (): bool
    {
        return $this->published;
    }

    function setPublished (bool $published): self
    {
        $this->published = $published;

        return $this;
    }

    function getProblem (): ?int
    {
        return $this->problem;
    }

    function setProblem (int $problem): self
    {
        $this->problem = $problem;

        return $this;
    }

    public function getClan (): ?string
    {
        return $this->clan;
    }

    public function setClan (string $clan = null): self
    {
        $this->clan = $clan;

        return $this;
    }

    public function getFormat (): ?string
    {
        return $this->format;
    }

    public function setFormat (string $format): self
    {
        $this->format = $format;

        return $this;
    }
}
