<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Pack
 *
 * @ORM\Table(name="packs")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PackRepository")
 * 
 * @author Alsciende <alsciende@icloud.com>
 */
class Pack
{

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * 
     * @Groups({"json"})
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     * 
     * @Groups({"json"})
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * @var int
     *
     * @ORM\Column(name="size", type="integer", nullable=true)
     */
    private $size;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreation", type="datetime")
     */
    private $dateCreation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateUpdate", type="datetime")
     */
    private $dateUpdate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateRelease", type="datetime", nullable=true)
     */
    private $dateRelease;

    /**
     * @var int
     *
     * @ORM\Column(name="ffgId", type="integer", nullable=true)
     */
    private $ffgId;

    /**
     * @var \AppBundle\Entity\Cycle
     *
     * @ORM\ManyToOne(targetEntity="Cycle", fetch="EAGER")
     * @ORM\JoinColumn(name="cycle_code", referencedColumnName="code")
     */
    private $cycle;

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
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return Pack
     */
    public function setDateCreation ($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime
     */
    public function getDateCreation ()
    {
        return $this->dateCreation;
    }

    /**
     * Set dateUpdate
     *
     * @param \DateTime $dateUpdate
     *
     * @return Pack
     */
    public function setDateUpdate ($dateUpdate)
    {
        $this->dateUpdate = $dateUpdate;

        return $this;
    }

    /**
     * Get dateUpdate
     *
     * @return \DateTime
     */
    public function getDateUpdate ()
    {
        return $this->dateUpdate;
    }

    /**
     * Set dateRelease
     *
     * @param \DateTime $dateRelease
     *
     * @return Pack
     */
    public function setDateRelease ($dateRelease)
    {
        $this->dateRelease = $dateRelease;

        return $this;
    }

    /**
     * Get dateRelease
     *
     * @return \DateTime
     */
    public function getDateRelease ()
    {
        return $this->dateRelease;
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
     * @param \AppBundle\Entity\Cycle $cycle
     * 
     * @return Pack
     */
    function setCycle (\AppBundle\Entity\Cycle $cycle)
    {
        $this->cycle = $cycle;

        return $this;
    }

}
