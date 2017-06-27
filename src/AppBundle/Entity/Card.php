<?php

namespace AppBundle\Entity;

use AppBundle\Model\SlotElementInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Alsciende\SerializerBundle\Annotation\Source;
use JMS\Serializer\Annotation as JMS;

/**
 * Card
 *
 * @ORM\Table(name="cards")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CardRepository")
 *
 * @Source(break="code")
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessorOrder("alphabetical")
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Card implements SlotElementInterface
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
    const ELEMENT__WATER = 'water';

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

    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="code", type="string", length=255, unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     *
     * @Source(type="string")
     *
     * @JMS\Expose
     */
    private $code;

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
     * @ORM\Column(name="type_code", type="text", nullable=false)
     *
     * @Source(type="string")
     *
     * @JMS\Expose
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="clan_code", type="text", nullable=true)
     *
     * @Source(type="string")
     *
     * @JMS\Expose
     */
    private $clan;

    /**
     * @var string
     *
     * @ORM\Column(name="element_code", type="text", nullable=true)
     *
     * @Source(type="string")
     *
     * @JMS\Expose
     */
    private $element;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_unique", type="boolean", nullable=false)
     *
     * @Source(type="boolean")
     *
     * @JMS\Expose
     */
    private $isUnique;

    /**
     * @var string
     *
     * @ORM\Column(name="side_code", type="string", nullable=true)
     *
     * @Source(type="string")
     *
     * @JMS\Expose
     */
    private $side;

    /**
     * @var string
     *
     * @ORM\Column(name="keywords", type="string", nullable=true)
     *
     * @Source(type="string")
     *
     * @JMS\Expose
     */
    private $keywords;

    /**
     * @var string
     *
     * @ORM\Column(name="illustrator", type="string", nullable=true)
     *
     * @Source(type="string")
     *
     * @JMS\Expose
     */
    private $illustrator;

    /**
     * @var integer
     *
     * @ORM\Column(name="military_strength", type="smallint", nullable=true)
     *
     * @Source(type="integer")
     *
     * @JMS\Expose
     */
    private $militaryStrength;

    /**
     * @var integer
     *
     * @ORM\Column(name="political_strength", type="smallint", nullable=true)
     *
     * @Source(type="integer")
     *
     * @JMS\Expose
     */
    private $politicalStrength;

    /**
     * @var integer
     *
     * @ORM\Column(name="province_strength", type="smallint", nullable=true)
     *
     * @Source(type="integer")
     *
     * @JMS\Expose
     */
    private $provinceStrength;

    /**
     * @var string
     *
     * @ORM\Column(name="military_strength_mod", type="string", nullable=true)
     *
     * @Source(type="string")
     *
     * @JMS\Expose
     */
    private $militaryStrengthMod;

    /**
     * @var string
     *
     * @ORM\Column(name="political_strength_mod", type="string", nullable=true)
     *
     * @Source(type="string")
     *
     * @JMS\Expose
     */
    private $politicalStrengthMod;

    /**
     * @var string
     *
     * @ORM\Column(name="province_strength_mod", type="string", nullable=true)
     *
     * @Source(type="string")
     *
     * @JMS\Expose
     */
    private $provinceStrengthMod;

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
     */
    private $deckLimit;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="PackCard", mappedBy="card", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    private $packCards;

    /**
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Review", mappedBy="card", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    private $reviews;

    /**
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Ruling", mappedBy="card", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    private $rulings;

    function __construct ()
    {
        $this->packCards = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->rulings = new ArrayCollection();
    }

    public function setCode (string $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function setName (string $name): self
    {
        $this->name = $name;
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

    public function setType (string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function setClan (string $clan = null): self
    {
        $this->clan = $clan;
        return $this;
    }

    public function setElement (string $element = null): self
    {
        $this->element = $element;
        return $this;
    }

    public function setIsUnique (bool $isUnique): self
    {
        $this->isUnique = $isUnique;
        return $this;
    }

    public function setSide (string $side = null): self
    {
        $this->side = $side;
        return $this;
    }

    public function setKeywords (string $keywords): self
    {
        $this->keywords = $keywords;
        return $this;
    }

    public function setIllustrator (string $illustrator): self
    {
        $this->illustrator = $illustrator;
        return $this;
    }

    public function setMilitaryStrength (int $militaryStrength): self
    {
        $this->militaryStrength = $militaryStrength;
        return $this;
    }

    public function setPoliticalStrength (int $politicalStrength): self
    {
        $this->politicalStrength = $politicalStrength;
        return $this;
    }

    public function setProvinceStrength (int $provinceStrength): self
    {
        $this->provinceStrength = $provinceStrength;
        return $this;
    }

    public function setMilitaryStrengthMod (string $militaryStrengthMod): self
    {
        $this->militaryStrengthMod = $militaryStrengthMod;
        return $this;
    }

    public function setPoliticalStrengthMod (string $politicalStrengthMod): self
    {
        $this->politicalStrengthMod = $politicalStrengthMod;
        return $this;
    }

    public function setProvinceStrengthMod (string $provinceStrengthMod): self
    {
        $this->provinceStrengthMod = $provinceStrengthMod;
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

    public function setInfluenceCost (int $influenceCost): self
    {
        $this->influenceCost = $influenceCost;
        return $this;
    }

    public function getCode (): string
    {
        return $this->code;
    }

    public function getName (): string
    {
        return $this->name;
    }

    public function getCost (): int
    {
        return $this->cost;
    }

    public function getText (): string
    {
        return $this->text;
    }

    public function getType (): string
    {
        return $this->type;
    }

    public function getClan (): ?string
    {
        return $this->clan;
    }

    public function getElement (): ?string
    {
        return $this->element;
    }

    public function isUnique (): bool
    {
        return $this->isUnique;
    }

    public function getSide (): ?string
    {
        return $this->side;
    }

    public function getKeywords (): string
    {
        return $this->keywords;
    }

    public function getIllustrator (): string
    {
        return $this->illustrator;
    }

    public function getMilitaryStrength (): int
    {
        return $this->militaryStrength;
    }

    public function getPoliticalStrength (): int
    {
        return $this->politicalStrength;
    }

    public function getProvinceStrength (): int
    {
        return $this->provinceStrength;
    }

    public function getMilitaryStrengthMod (): string
    {
        return $this->militaryStrengthMod;
    }

    public function getPoliticalStrengthMod (): string
    {
        return $this->politicalStrengthMod;
    }

    public function getProvinceStrengthMod (): string
    {
        return $this->provinceStrengthMod;
    }

    public function getGlory (): int
    {
        return $this->glory;
    }

    public function getHonor (): int
    {
        return $this->honor;
    }

    public function getFate (): int
    {
        return $this->fate;
    }

    public function getInfluencePool (): int
    {
        return $this->influencePool;
    }

    public function getInfluenceCost (): int
    {
        return $this->influenceCost;
    }

    public function setDeckLimit (int $deckLimit): self
    {
        $this->deckLimit = $deckLimit;

        return $this;
    }

    public function getDeckLimit (): int
    {
        return $this->deckLimit;
    }

    /**
     *
     * @return \AppBundle\Model\PackCardSlotCollectionDecorator
     */
    function getPackCards ()
    {
        return new \AppBundle\Model\PackCardSlotCollectionDecorator($this->packCards->toArray());
    }

    /**
     *
     * @param \AppBundle\Entity\PackCard $packCard
     * @return Card
     */
    function addPackCard (PackCard $packCard)
    {
        $this->packCards[] = $packCard;

        return $this;
    }

    /**
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    function getReviews (): \Doctrine\Common\Collections\Collection
    {
        return $this->reviews;
    }

    /**
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    function getRulings (): \Doctrine\Common\Collections\Collection
    {
        return $this->rulings;
    }

    /**
     *
     * @param \Doctrine\Common\Collections\Collection $reviews
     * @return Card
     */
    function setReviews (\Doctrine\Common\Collections\Collection $reviews)
    {
        $this->reviews = $reviews;
        return $this;
    }

    /**
     *
     * @param \Doctrine\Common\Collections\Collection $rulings
     * @return Card
     */
    function setRulings (\Doctrine\Common\Collections\Collection $rulings)
    {
        $this->rulings = $rulings;
        return $this;
    }

    /**
     * Packs including the card
     *
     * @JMS\VirtualProperty
     * @JMS\Type("array")
     * @return array
     */
    function getPacks()
    {
        return $this->getPackCards()->getQuantities();
    }

    /**
     * cycles including the card
     *
     * @JMS\VirtualProperty
     * @JMS\Type("array")
     * @return array
     */
    function getCycles()
    {
        $cycles = [];
        /* @var $packCard PackCard */
        foreach($this->getPackCards() as $packCard) {
            $cycleCode = $packCard->getPack()->getCycle()->getCode();
            if(!key_exists($cycleCode, $cycles)) {
                $cycles[$cycleCode] = 0;
            }
            $cycles[$cycleCode] += $packCard->getQuantity();
        }
        return $cycles;
    }

}
