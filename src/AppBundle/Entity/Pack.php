<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Alsciende\SerializerBundle\Annotation\Source;
use JMS\Serializer\Annotation as JMS;

/**
 * Pack
 *
 * @ORM\Table(name="packs")
 * @ORM\Entity
 *
 * @Source
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessorOrder("alphabetical")
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Pack
{
    use TimestampableEntity;

    /**
     * @var string
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
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     *
     * @Source(type="string")
     *
     * @JMS\Expose
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer")
     *
     * @Source(type="integer")
     *
     * @JMS\Expose
     */
    private $position;

    /**
     * @var int
     *
     * @ORM\Column(name="size", type="integer", nullable=true)
     *
     * @Source(type="integer")
     *
     * @JMS\Expose
     */
    private $size;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="released_at", type="datetime", nullable=true)
     *
     * @Source(type="date")
     *
     * @JMS\Expose
     */
    private $releasedAt;

    /*
     * @var int
     *
     * @ORM\Column(name="ffg_id", type="integer", nullable=true)
     *
     * @Source(type="integer")
     */
    private $ffgId;

    /**
     * @var Cycle
     *
     * @ORM\ManyToOne(targetEntity="Cycle")
     * @ORM\JoinColumn(name="cycle_code", referencedColumnName="code")
     *
     * @Source(type="association")
     */
    private $cycle;

    /**
     * @var PackCard[]
     *
     * @ORM\OneToMany(targetEntity="PackCard", mappedBy="pack", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    private $cards;

    function __construct ()
    {
        $this->cards = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function setCode (string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getCode (): string
    {
        return $this->code;
    }

    public function setName (string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName (): string
    {
        return $this->name;
    }

    public function setPosition (int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getPosition (): int
    {
        return $this->position;
    }

    public function setSize (int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getSize (): int
    {
        return $this->size;
    }

    function getReleasedAt (): DateTime
    {
        return $this->releasedAt;
    }

    function setReleasedAt (DateTime $releasedAt): self
    {
        $this->releasedAt = $releasedAt;

        return $this;
    }

    public function setFfgId (int $ffgId): self
    {
        $this->ffgId = $ffgId;

        return $this;
    }

    public function getFfgId (): int
    {
        return $this->ffgId;
    }

    function getCycle (): Cycle
    {
        return $this->cycle;
    }

    /**
     * @JMS\VirtualProperty()
     */
    function getCycleCode (): string
    {
        return $this->cycle ? $this->cycle->getCode() : null;
    }

    function setCycle (Cycle $cycle): self
    {
        $this->cycle = $cycle;

        return $this;
    }

    /**
     * @return PackCard[]
     */
    function getCards (): Collection
    {
        return $this->cards;
    }
}
