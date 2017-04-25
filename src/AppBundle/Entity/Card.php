<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Alsciende\SerializerBundle\Annotation\Source;

/**
 * Card
 *
 * @ORM\Table(name="cards")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CardRepository")
 * 
 * @Source(break="code")
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Card
{

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
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="cost", type="integer", nullable=true)
     * 
     * @Source(type="integer")
     */
    private $cost;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", nullable=true)
     * 
     * @Source(type="string")
     */
    private $text;

    /**
     * @var \AppBundle\Entity\Type
     *
     * @ORM\ManyToOne(targetEntity="Type")
     * @ORM\JoinColumn(name="type_code", referencedColumnName="code")
     * 
     * @Source(type="association")
     */
    private $type;

    /**
     * @var \AppBundle\Entity\Clan
     *
     * @ORM\ManyToOne(targetEntity="Clan")
     * @ORM\JoinColumn(name="clan_code", referencedColumnName="code", nullable=true)
     * 
     * @Source(type="association")
     */
    private $clan;

    /**
     * @var \AppBundle\Entity\Element
     *
     * @ORM\ManyToOne(targetEntity="Element")
     * @ORM\JoinColumn(name="element_code", referencedColumnName="code", nullable=true)
     * 
     * @Source(type="association")
     */
    private $element;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_unique", type="boolean", nullable=false)
     * 
     * @Source(type="boolean")
     */
    private $isUnique;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_destiny", type="boolean", nullable=false)
     * 
     * @Source(type="boolean")
     */
    private $isDestiny;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_conflict", type="boolean", nullable=false)
     * 
     * @Source(type="boolean")
     */
    private $isConflict;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="keywords", type="string", nullable=true)
     * 
     * @Source(type="string")
     */
    private $keywords;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="illustrator", type="string", nullable=true)
     * 
     * @Source(type="string")
     */
    private $illustrator;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="military_strength", type="smallint", nullable=true)
     * 
     * @Source(type="integer")
     */
    private $militaryStrength;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="political_strength", type="smallint", nullable=true)
     * 
     * @Source(type="integer")
     */
    private $politicalStrength;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="province_strength", type="smallint", nullable=true)
     * 
     * @Source(type="integer")
     */
    private $provinceStrength;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="military_strength_mod", type="string", nullable=true)
     * 
     * @Source(type="string")
     */
    private $militaryStrengthMod;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="political_strength_mod", type="string", nullable=true)
     * 
     * @Source(type="string")
     */
    private $politicalStrengthMod;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="province_strength_mod", type="string", nullable=true)
     * 
     * @Source(type="string")
     */
    private $provinceStrengthMod;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="glory", type="smallint", nullable=true)
     * 
     * @Source(type="integer")
     */
    private $glory;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="honor", type="smallint", nullable=true)
     * 
     * @Source(type="integer")
     */
    private $honor;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="fate", type="smallint", nullable=true)
     * 
     * @Source(type="integer")
     */
    private $fate;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="influence", type="smallint", nullable=true)
     * 
     * @Source(type="integer")
     */
    private $influence;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="influence_cost", type="smallint", nullable=true)
     * 
     * @Source(type="integer")
     */
    private $influenceCost;
    
    /**
     * Set code
     *
     * @param string $code
     *
     * @return Card
     */
    public function setCode ($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode ()
    {
        return $this->code;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Card
     */
    public function setName ($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName ()
    {
        return $this->name;
    }

    /**
     * Set cost
     *
     * @param integer $cost
     *
     * @return Card
     */
    public function setCost ($cost)
    {
        $this->cost = $cost;

        return $this;
    }

    /**
     * Get cost
     *
     * @return int
     */
    public function getCost ()
    {
        return $this->cost;
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return Card
     */
    public function setText ($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText ()
    {
        return $this->text;
    }

    /**
     * Get type
     * 
     * @return Type
     */
    function getType ()
    {
        return $this->type;
    }

    /**
     * Get clan
     * 
     * @return Clan
     */
    function getClan ()
    {
        return $this->clan;
    }

    /**
     * Set type
     * 
     * @param \AppBundle\Entity\Type $type
     *
     * @return Card
     */
    function setType (\AppBundle\Entity\Type $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Set clan
     * 
     * @param \AppBundle\Entity\Clan $clan
     *
     * @return Card
     */
    function setClan (\AppBundle\Entity\Clan $clan)
    {
        $this->clan = $clan;

        return $this;
    }
    
    function getElement ()
    {
        return $this->element;
    }

    function getIsUnique ()
    {
        return $this->isUnique;
    }

    function getIsDestiny ()
    {
        return $this->isDestiny;
    }

    function getIsConflict ()
    {
        return $this->isConflict;
    }

    function getKeywords ()
    {
        return $this->keywords;
    }

    function getIllustrator ()
    {
        return $this->illustrator;
    }

    function getMilitaryStrength ()
    {
        return $this->militaryStrength;
    }

    function getPoliticalStrength ()
    {
        return $this->politicalStrength;
    }

    function getProvinceStrength ()
    {
        return $this->provinceStrength;
    }

    function getMilitaryStrengthMod ()
    {
        return $this->militaryStrengthMod;
    }

    function getPoliticalStrengthMod ()
    {
        return $this->politicalStrengthMod;
    }

    function getProvinceStrengthMod ()
    {
        return $this->provinceStrengthMod;
    }

    function getGlory ()
    {
        return $this->glory;
    }

    function getHonor ()
    {
        return $this->honor;
    }

    function getFate ()
    {
        return $this->fate;
    }

    function getInfluence ()
    {
        return $this->influence;
    }

    function getInfluenceCost ()
    {
        return $this->influenceCost;
    }

    function setElement (\AppBundle\Entity\Element $element)
    {
        $this->element = $element;
    }

    function setIsUnique ($isUnique)
    {
        $this->isUnique = $isUnique;
    }

    function setIsDestiny ($isDestiny)
    {
        $this->isDestiny = $isDestiny;
    }

    function setIsConflict ($isConflict)
    {
        $this->isConflict = $isConflict;
    }

    function setKeywords ($keywords)
    {
        $this->keywords = $keywords;
    }

    function setIllustrator ($illustrator)
    {
        $this->illustrator = $illustrator;
    }

    function setMilitaryStrength ($militaryStrength)
    {
        $this->militaryStrength = $militaryStrength;
    }

    function setPoliticalStrength ($politicalStrength)
    {
        $this->politicalStrength = $politicalStrength;
    }

    function setProvinceStrength ($provinceStrength)
    {
        $this->provinceStrength = $provinceStrength;
    }

    function setMilitaryStrengthMod ($militaryStrengthMod)
    {
        $this->militaryStrengthMod = $militaryStrengthMod;
    }

    function setPoliticalStrengthMod ($politicalStrengthMod)
    {
        $this->politicalStrengthMod = $politicalStrengthMod;
    }

    function setProvinceStrengthMod ($provinceStrengthMod)
    {
        $this->provinceStrengthMod = $provinceStrengthMod;
    }

    function setGlory ($glory)
    {
        $this->glory = $glory;
    }

    function setHonor ($honor)
    {
        $this->honor = $honor;
    }

    function setFate ($fate)
    {
        $this->fate = $fate;
    }

    function setInfluence ($influence)
    {
        $this->influence = $influence;
    }

    function setInfluenceCost ($influenceCost)
    {
        $this->influenceCost = $influenceCost;
    }

}
