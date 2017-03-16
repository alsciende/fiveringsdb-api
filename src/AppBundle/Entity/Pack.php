<?php

namespace AppBundle\Entity;

use AppBundle\Model\CardSlotCollection;
use AppBundle\Repository\PackRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Alsciende\SerializerBundle\Annotation\Source;

/**
 * Pack
 *
 * @ORM\Table(name="packs")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PackRepository")
 * 
 * @Source()
 * 
 * @author Alsciende <alsciende@icloud.com>
 */
class Pack
{
    use TimestampableEntity;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * 
     * @Groups({"alsciende_serializer"})
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     * 
     * @Groups({"alsciende_serializer"})
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer")
     * 
     * @Groups({"alsciende_serializer"})
     */
    private $position;

    /**
     * @var int
     *
     * @ORM\Column(name="size", type="integer", nullable=true)
     * 
     * @Groups({"alsciende_serializer"})
     */
    private $size;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="release_at", type="datetime", nullable=true)
     */
    private $releaseAt;

    /**
     * @var int
     *
     * @ORM\Column(name="ffgId", type="integer", nullable=true)
     */
    private $ffgId;

    /**
     * @var Cycle
     *
     * @ORM\ManyToOne(targetEntity="Cycle", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="cycle_code", referencedColumnName="code")
     */
    private $cycle;

    /**
     * @var PackSlots[]
     * 
     * @ORM\OneToMany(targetEntity="PackSlot", mappedBy="pack", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    private $slots;

    function __construct ()
    {
        $this->cards = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Pack
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
     * @return Pack
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
     * Set position
     *
     * @param integer $position
     *
     * @return Pack
     */
    public function setPosition ($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return int
     */
    public function getPosition ()
    {
        return $this->position;
    }

    /**
     * Set size
     *
     * @param integer $size
     *
     * @return Pack
     */
    public function setSize ($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return int
     */
    public function getSize ()
    {
        return $this->size;
    }
    
    /**
     * 
     * @return DateTime
     */
    function getReleaseAt ()
    {
        return $this->releaseAt;
    }

    /**
     * 
     * @param DateTime $releaseAt
     */
    function setReleaseAt (DateTime $releaseAt)
    {
        $this->releaseAt = $releaseAt;
        
        return $this;
    }

    /**
     * Set ffgId
     *
     * @param integer $ffgId
     *
     * @return Pack
     */
    public function setFfgId ($ffgId)
    {
        $this->ffgId = $ffgId;

        return $this;
    }

    /**
     * Get ffgId
     *
     * @return int
     */
    public function getFfgId ()
    {
        return $this->ffgId;
    }

    /**
     * Get cycle
     * 
     * @return Cycle
     */
    function getCycle ()
    {
        return $this->cycle;
    }

    /**
     * Set cycle
     * 
     * @param Cycle $cycle
     * 
     * @return Pack
     */
    function setCycle (Cycle $cycle)
    {
        $this->cycle = $cycle;

        return $this;
    }

}
