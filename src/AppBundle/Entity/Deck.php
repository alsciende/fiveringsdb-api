<?php

namespace AppBundle\Entity;

use AppBundle\Model\CardSlotCollectionDecorator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * A Deck, private (minorVersion > 0) or public (minorVersion == 0)
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DeckRepository")
 * @ORM\Table(name="decks", indexes={
 *          @ORM\Index(columns={"published"}),
 *          @ORM\Index(columns={"published","published_at"})
 *     })
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Deck
{
    const FORMAT_STANDARD = 'standard';
    const FORMAT_SINGLE_CORE = 'single-core';

    /**
     * Unique identifier of the deck
     *
     * @var string
     *
     * @ORM\Column(name="id", type="string", length=255, unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * Name of the deck
     *
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * Markdown-formatted description of the deck
     *
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $format;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * The cards used by the deck
     *
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="DeckCard", mappedBy="deck", cascade={"persist", "remove", "merge"})
     */
    private $deckCards;

    /**
     * The owner of the deck
     *
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", fetch="EAGER")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * The strain of the deck
     *
     * @var Strain|null
     *
     * @ORM\ManyToOne(targetEntity="Strain", inversedBy="decks")
     * @ORM\JoinColumn(name="strain_id", referencedColumnName="id", nullable=true)
     */
    private $strain;

    /**
     * @var Collection|DeckLike[]
     *
     * @ORM\OneToMany(targetEntity="DeckLike", mappedBy="deck", cascade={"persist", "remove", "merge"})
     */
    private $deckLikes;

    /**
     * @var integer
     */
    private $nbLikes;

    /**
     * @var integer
     */
    private $nbComments;

    /**
     * Comments on the deck
     *
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="deck", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    private $comments;

    /**
     * The major version of the deck. Incremented with each publication.
     *
     * @var integer
     *
     * @ORM\Column(name="major_version", type="integer", nullable=false)
     */
    private $majorVersion;

    /**
     * The minor version of the deck. 0 for a public deck. Incremented with each save.
     *
     * @var integer
     *
     * @ORM\Column(name="minor_version", type="integer", nullable=false)
     */
    private $minorVersion;

    /**
     * Whether the deck is published
     *
     * @var boolean
     *
     * @ORM\Column(name="published", type="boolean", nullable=false)
     */
    private $published;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="published_at", type="datetime", nullable=true)
     */
    private $publishedAt;

    /**
     * A number indicating a problem with the deck
     *
     * @var integer
     *
     * @ORM\Column(name="problem", type="integer", nullable=false)
     */
    private $problem;

    /**
     * Primary Clan of the deck
     *
     * @var string|null
     *
     * @ORM\Column(name="primary_clan", type="string", nullable=true)
     */
    private $primaryClan;

    /**
     * Secondary Clan of the deck
     *
     * @var string|null
     *
     * @ORM\Column(name="secondary_clan", type="string", nullable=true)
     */
    private $secondaryClan;

    /**
     * @var Collection|Activity[]
     * @ORM\OneToMany(targetEntity="Activity", mappedBy="deck", cascade={"remove"})
     */
    private $activities;

    function __construct ()
    {
        $this->name = 'Default name';
        $this->format = 'standard';
        $this->description = '';
        $this->majorVersion = 0;
        $this->minorVersion = 1;
        $this->published = false;
        $this->createdAt = new \DateTime();
        $this->deckCards = new ArrayCollection();
        $this->activities = new ArrayCollection();
        $this->nbLikes = 0;
        $this->nbComments = 0;
    }

    public function getNbLikes (): ?int
    {
        return $this->nbLikes;
    }

    public function setNbLikes (int $nbLikes): self
    {
        $this->nbLikes = $nbLikes;

        return $this;
    }

    /** @return Collection|DeckLike[] */
    public function getDeckLikes (): Collection
    {
        return $this->deckLikes;
    }

    public function addDeckLike (DeckLike $deckLike): self
    {
        if ($this->deckLikes->contains($deckLike) === false) {
            $this->deckLikes->add($deckLike);
            $deckLike->setDeck($this);
        }

        return $this;
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

    function getName (): string
    {
        return $this->name;
    }

    function setName (string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /** @return CardSlotCollectionDecorator|DeckCard[] */
    public function getDeckCards (): CardSlotCollectionDecorator
    {
        if ($this->deckCards === null) {
            $this->deckCards = new ArrayCollection();
        }

        return new CardSlotCollectionDecorator($this->deckCards->toArray());
    }

    public function setDeckCards (Collection $deckCards): self
    {
        if (!$this->deckCards instanceof Collection) {
            $this->deckCards = new ArrayCollection();
        }

        if ($this->deckCards->count() > 0) {
            throw new \Exception('setDeckCards called on a non-empty Deck.');
        }

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

    function getUser (): User
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

    function setStrain (Strain $strain = null): self
    {
        $this->strain = $strain;

        return $this;
    }

    /** @return Collection|Comment[] */
    public function getComments (): Collection
    {
        return $this->comments;
    }

    public function addComment (Comment $comment): self
    {
        if ($this->comments->contains($comment) === false) {
            $this->comments->add($comment);
            $comment->setDeck($this);
        }

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

    function setMajorVersion (int $majorVersion): self
    {
        $this->majorVersion = $majorVersion;

        return $this;
    }

    function getMinorVersion (): int
    {
        return $this->minorVersion;
    }

    function setMinorVersion (int $minorVersion): self
    {
        $this->minorVersion = $minorVersion;

        return $this;
    }

    function getVersion (): string
    {
        return $this->majorVersion . '.' . $this->minorVersion;
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

    public function getPrimaryClan (): ?string
    {
        return $this->primaryClan;
    }

    public function setPrimaryClan (string $primaryClan = null): self
    {
        $this->primaryClan = $primaryClan;

        return $this;
    }

    public function getSecondaryClan (): ?string
    {
        return $this->secondaryClan;
    }

    public function setSecondaryClan (string $secondaryClan = null): self
    {
        $this->secondaryClan = $secondaryClan;

        return $this;
    }

    public function getFormat (): string
    {
        return $this->format;
    }

    public function setFormat (string $format): self
    {
        $this->format = $format;

        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getNbComments (): ?int
    {
        return $this->nbComments;
    }

    public function setNbComments (int $nbComments): self
    {
        $this->nbComments = $nbComments;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPublishedAt (): ?\DateTime
    {
        return $this->publishedAt;
    }

    /**
     * @param \DateTime $publishedAt
     *
     * @return self
     */
    public function setPublishedAt (\DateTime $publishedAt = null): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /** @return Collection|Activity[] */
    public function getActivities (): Collection
    {
        return $this->activities;
    }

    /** @param Collection|Activity[] $activities */
    public function setActivities (Collection $activities): self
    {
        $this->clearActivitys();
        foreach ($activities as $activity) {
            $this->addActivity($activity);
        }

        return $this;
    }

    public function clearActivitys (): self
    {
        foreach ($this->getActivities() as $activity) {
            $this->removeActivity($activity);
        }
        $this->activities->clear();

        return $this;
    }

    public function removeActivity (Activity $activity): self
    {
        if ($this->activities->contains($activity)) {
            $this->activities->removeElement($activity);
        }

        return $this;
    }

    public function addActivity (Activity $activity): self
    {
        if ($this->activities->contains($activity) === false) {
            $this->activities->add($activity);
        }

        return $this;
    }
}
