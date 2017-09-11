<?php

namespace AppBundle\Entity;

use AppBundle\Model\PackCardSlotCollectionDecorator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Alsciende\SerializerBundle\Annotation\Source;
use JMS\Serializer\Annotation as JMS;
use AppBundle\Behavior\Entity\ToStringNameTrait;

/**
 * Card
 *
 * @ORM\Table(name="cards")
 * @ORM\Entity()
 *
 * @Source(break="id")
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessorOrder("alphabetical")
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Card
{
    const TYPE_ATTACHMENT = 'attachment';
    const TYPE_CHARACTER = 'character';
    const TYPE_EVENT = 'event';
    const TYPE_HOLDING = 'holding';
    const TYPE_PROVINCE = 'province';
    const TYPE_ROLE = 'role';
    const TYPE_STRONGHOLD = 'stronghold';

    const ELEMENT_AIR = 'air';
    const ELEMENT_EARTH = 'earth';
    const ELEMENT_FIRE = 'fire';
    const ELEMENT_VOID = 'void';
    const ELEMENT_WATER = 'water';

    const SIDE_CONFLICT = 'conflict';
    const SIDE_DYNASTY = 'dynasty';

    const CLAN_CRAB = 'crab';
    const CLAN_CRANE = 'crane';
    const CLAN_DRAGON = 'dragon';
    const CLAN_LION = 'lion';
    const CLAN_NEUTRAL = 'neutral';
    const CLAN_PHOENIX = 'phoenix';
    const CLAN_SCORPION = 'scorpion';
    const CLAN_UNICORN = 'unicorn';

    use TimestampableEntity;
    use ToStringNameTrait;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="id", type="string", length=255, unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     *
     * @Source(type="string")
     *
     * @JMS\Expose
     * @JMS\Groups({"Default","id_group"})
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="name", type="string", length=255)
     *
     * @Source(type="string")
     *
     * @JMS\Expose
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="name_canonical", type="string", length=255)
     *
     * @JMS\Expose
     */
    private $nameCanonical;

    /**
     * @var int
     *
     * @ORM\Column(name="cost", type="integer", nullable=true)
     *
     * @Source(type="integer")
     *
     * @JMS\Expose
     */
    private $cost;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", nullable=true)
     *
     * @Source(type="string")
     *
     * @JMS\Expose
     */
    private $text;

    /**
     * @var string
     *
     * @ORM\Column(name="text_canonical", type="text", nullable=true)
     *
     * @JMS\Expose
     */
    private $textCanonical;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="text", nullable=false)
     *
     * @Source(type="string")
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="clan", type="text", nullable=false)
     *
     * @Source(type="string")
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    private $clan;

    /**
     * @var string
     *
     * @ORM\Column(name="element", type="text", nullable=true)
     *
     * @Source(type="string")
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    private $element;

    /**
     * @var boolean
     *
     * @ORM\Column(name="unicity", type="boolean", nullable=false)
     *
     * @Source(type="boolean")
     *
     * @JMS\Expose
     */
    private $unicity;

    /**
     * @var string
     *
     * @ORM\Column(name="side", type="string", nullable=false)
     *
     * @Source(type="string")
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    private $side;

    /**
     * @var array
     *
     * @ORM\Column(name="traits", type="simple_array", nullable=true)
     *
     * @Source(type="array")
     *
     * @JMS\Expose
     */
    private $traits;

    /**
     * @var integer
     *
     * @ORM\Column(name="military", type="smallint", nullable=true)
     *
     * @Source(type="integer")
     *
     * @JMS\Expose
     */
    private $military;

    /**
     * @var integer
     *
     * @ORM\Column(name="political", type="smallint", nullable=true)
     *
     * @Source(type="integer")
     *
     * @JMS\Expose
     */
    private $political;

    /**
     * @var integer
     *
     * @ORM\Column(name="strength", type="smallint", nullable=true)
     *
     * @Source(type="integer")
     *
     * @JMS\Expose
     */
    private $strength;

    /**
     * @var string
     *
     * @ORM\Column(name="military_bonus", type="string", nullable=true)
     *
     * @Source(type="string")
     *
     * @JMS\Expose
     */
    private $militaryBonus;

    /**
     * @var string
     *
     * @ORM\Column(name="political_bonus", type="string", nullable=true)
     *
     * @Source(type="string")
     *
     * @JMS\Expose
     */
    private $politicalBonus;

    /**
     * @var string
     *
     * @ORM\Column(name="strength_bonus", type="string", nullable=true)
     *
     * @Source(type="string")
     *
     * @JMS\Expose
     */
    private $strengthBonus;

    /**
     * @var integer
     *
     * @ORM\Column(name="glory", type="smallint", nullable=true)
     *
     * @Source(type="integer")
     *
     * @JMS\Expose
     */
    private $glory;

    /**
     * @var integer
     *
     * @ORM\Column(name="honor", type="smallint", nullable=true)
     *
     * @Source(type="integer")
     *
     * @JMS\Expose
     */
    private $honor;

    /**
     * @var integer
     *
     * @ORM\Column(name="fate", type="smallint", nullable=true)
     *
     * @Source(type="integer")
     *
     * @JMS\Expose
     */
    private $fate;

    /**
     * @var integer
     *
     * @ORM\Column(name="influence_pool", type="smallint", nullable=true)
     *
     * @Source(type="integer")
     *
     * @JMS\Expose
     * @JMS\Type("int")
     */
    private $influencePool;

    /**
     * @var integer
     *
     * @ORM\Column(name="influence_cost", type="smallint", nullable=true)
     *
     * @Source(type="integer")
     *
     * @JMS\Expose
     * @JMS\Type("int")
     */
    private $influenceCost;

    /**
     * @var integer
     *
     * @ORM\Column(name="deck_limit", type="smallint", nullable=false)
     *
     * @Source(type="integer")
     *
     * @JMS\Expose
     * @JMS\Type("int")
     */
    private $deckLimit;

    /**
     * @var string
     *
     * @ORM\Column(name="role_restriction", type="string", nullable=true)
     *
     * @Source(type="string")
     *
     * @JMS\Expose()
     * @JMS\Type("string")
     */
    private $roleRestriction;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="PackCard", mappedBy="card", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     *
     * @JMS\Expose
     * @JMS\Groups({"packs_group"})
     */
    private $packCards;

    /**
     *
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Review", mappedBy="card", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     *
     * @JMS\Expose
     * @JMS\Groups({"reviews_group"})
     */
    private $reviews;

    /**
     *
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Ruling", mappedBy="card", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     *
     * @JMS\Expose
     * @JMS\Groups({"rulings_group"})
     */
    private $rulings;

    function __construct ()
    {
        $this->packCards = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->rulings = new ArrayCollection();
        $this->deckLimit = 3;
        $this->traits = [];
        $this->unicity = false;
    }

    public function setId (string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function setName (string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setNameCanonical (string $nameCanonical): self
    {
        $this->nameCanonical = $nameCanonical;

        return $this;
    }

    public function setCost (int $cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    public function setText (string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function setTextCanonical (string $textCanonical): self
    {
        $this->textCanonical = $textCanonical;

        return $this;
    }

    public function setType (string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function setClan (string $clan): self
    {
        $this->clan = $clan;

        return $this;
    }

    public function setElement (string $element = null): self
    {
        $this->element = $element;

        return $this;
    }

    public function setUnicity (bool $unicity): self
    {
        $this->unicity = $unicity;

        return $this;
    }

    public function setSide (string $side): self
    {
        $this->side = $side;

        return $this;
    }

    public function setTraits (array $traits): self
    {
        $this->traits = $traits;

        return $this;
    }

    public function setMilitary (int $military): self
    {
        $this->military = $military;

        return $this;
    }

    public function setPolitical (int $political): self
    {
        $this->political = $political;

        return $this;
    }

    public function setStrength (int $strength): self
    {
        $this->strength = $strength;

        return $this;
    }

    public function setMilitaryBonus (string $militaryBonus): self
    {
        $this->militaryBonus = $militaryBonus;

        return $this;
    }

    public function setPoliticalBonus (string $politicalBonus): self
    {
        $this->politicalBonus = $politicalBonus;

        return $this;
    }

    public function setStrengthBonus (string $strengthBonus): self
    {
        $this->strengthBonus = $strengthBonus;

        return $this;
    }

    public function setGlory (int $glory): self
    {
        $this->glory = $glory;

        return $this;
    }

    public function setHonor (int $honor): self
    {
        $this->honor = $honor;

        return $this;
    }

    public function setFate (int $fate): self
    {
        $this->fate = $fate;

        return $this;
    }

    public function setInfluencePool (int $influencePool): self
    {
        $this->influencePool = $influencePool;

        return $this;
    }

    public function setInfluenceCost (int $influenceCost = null): self
    {
        $this->influenceCost = $influenceCost;

        return $this;
    }

    public function getId (): string
    {
        return $this->id;
    }

    public function getName (): string
    {
        return $this->name;
    }

    public function getNameCanonical (): ?string
    {
        return $this->nameCanonical;
    }

    public function getCost (): ?int
    {
        return $this->cost;
    }

    public function getText (): ?string
    {
        return $this->text;
    }

    public function getTextCanonical (): ?string
    {
        return $this->textCanonical;
    }

    public function getType (): string
    {
        return $this->type;
    }

    public function getClan (): string
    {
        return $this->clan;
    }

    public function getElement (): ?string
    {
        return $this->element;
    }

    public function isUnicity (): bool
    {
        return $this->unicity;
    }

    public function getSide (): string
    {
        return $this->side;
    }

    public function getTraits (): array
    {
        return $this->traits;
    }

    public function hasTrait ($trait): bool
    {
        return in_array($trait, $this->traits);
    }

    public function getMilitary (): ?int
    {
        return $this->military;
    }

    public function getPolitical (): ?int
    {
        return $this->political;
    }

    public function getStrength (): ?int
    {
        return $this->strength;
    }

    public function getMilitaryBonus (): ?string
    {
        return $this->militaryBonus;
    }

    public function getPoliticalBonus (): ?string
    {
        return $this->politicalBonus;
    }

    public function getStrengthBonus (): ?string
    {
        return $this->strengthBonus;
    }

    public function getGlory (): ?int
    {
        return $this->glory;
    }

    public function getHonor (): ?int
    {
        return $this->honor;
    }

    public function getFate (): ?int
    {
        return $this->fate;
    }

    public function getInfluencePool (): ?int
    {
        return $this->influencePool;
    }

    public function getInfluenceCost (): ?int
    {
        return $this->influenceCost;
    }

    public function setDeckLimit (int $deckLimit): self
    {
        $this->deckLimit = $deckLimit;

        return $this;
    }

    public function getDeckLimit (): ?int
    {
        return $this->deckLimit;
    }

    public function getRoleRestriction (): ?string
    {
        return $this->roleRestriction;
    }

    public function setRoleRestriction (string $roleRestriction): self
    {
        $this->roleRestriction = $roleRestriction;

        return $this;
    }

    /**
     *
     * @return PackCardSlotCollectionDecorator
     */
    function getPackCards ()
    {
        return new PackCardSlotCollectionDecorator($this->packCards->toArray());
    }

    /**
     *
     * @param PackCard $packCard
     * @return Card
     */
    function addPackCard (PackCard $packCard)
    {
        $this->packCards[] = $packCard;

        return $this;
    }

    /**
     *
     * @return Collection
     */
    function getReviews (): Collection
    {
        return $this->reviews;
    }

    /**
     *
     * @return Collection
     */
    function getRulings (): Collection
    {
        return $this->rulings;
    }

    /**
     *
     * @param Collection $reviews
     * @return Card
     */
    function setReviews (Collection $reviews)
    {
        $this->reviews = $reviews;

        return $this;
    }

    /**
     *
     * @param Collection $rulings
     * @return Card
     */
    function setRulings (Collection $rulings)
    {
        $this->rulings = $rulings;

        return $this;
    }
}
