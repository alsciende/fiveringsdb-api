<?php

namespace AppBundle\Entity;

use Alsciende\SerializerBundle\Annotation\Source;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * Cycle
 *
 * @ORM\Entity()
 * @ORM\Table(name="cycles")
 *
 * @Source()
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Cycle
{
    use TimestampableEntity;

    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", length=255, unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     *
     * @Source(type="string")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     *
     * @Source(type="string")
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer")
     *
     * @Source(type="integer")
     */
    private $position;

    /**
     * @var int
     *
     * @ORM\Column(name="size", type="integer", nullable=true)
     *
     * @Source(type="integer")
     */
    private $size;

    /**
     * @var Collection|Pack[]
     *
     * @ORM\OneToMany(targetEntity="Pack", mappedBy="cycle")
     */
    private $packs;

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

    public function setPosition (int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function setSize (int $size): self
    {
        $this->size = $size;

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

    public function getPosition (): int
    {
        return $this->position;
    }

    public function getSize (): int
    {
        return $this->size;
    }

    public function addPack (Pack $pack): self
    {
        if ($this->packs->contains($pack) === false) {
            $this->packs->add($pack);
            $pack->setCycle($this);
        }

        return $this;
    }

    /** @return Collection|Pack[] */
    public function getPacks (): Collection
    {
        return $this->packs;
    }
}
