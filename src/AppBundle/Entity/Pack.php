<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Alsciende\SerializerBundle\Annotation\Source;
use JMS\Serializer\Annotation as JMS;

/**
 * Pack
 *
 * @ORM\Table(name="packs")
 * @ORM\Entity()
 *
 * @Source()
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
     * @ORM\JoinColumn(name="cycle_id", referencedColumnName="id", nullable=false)
     *
     * @Source(type="association")
     *
     * @JMS\Expose()
     * @JMS\Groups({"cycle_group"})
     */
    private $cycle;

    /**
     * @var PackCard[]
     *
     * @ORM\OneToMany(targetEntity="PackCard", mappedBy="pack", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     *
     * @JMS\Expose()
     * @JMS\Groups({"cards_group"})
     */
    private $cards;

    function __construct ()
    {
        $this->cards = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function setId (string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId (): string
    {
        return $this->id;
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
