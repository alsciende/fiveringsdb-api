<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Alsciende\SerializerBundle\Annotation\Source;
use JMS\Serializer\Annotation as JMS;


/**
 * Cycle
 *
 * @ORM\Table(name="cycles")
 * @ORM\Entity()
 *
 * @Source()
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessorOrder("alphabetical")
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
     *
     * @JMS\Expose()
     * @JMS\Groups({"Default","id_group"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     *
     * @Source(type="string")
     *
     * @JMS\Expose()
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer")
     *
     * @Source(type="integer")
     *
     * @JMS\Expose()
     */
    private $position;

    /**
     * @var int
     *
     * @ORM\Column(name="size", type="integer", nullable=true)
     *
     * @Source(type="integer")
     *
     * @JMS\Expose()
     */
    private $size;

    /**
     * @var Collection|Pack[]
     * @ORM\OneToMany(targetEntity="Pack", mappedBy="cycle")
     *
     * @JMS\Expose()
     * @JMS\Groups({"packs_group"})
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

    /** @param Collection|Pack[] $packs */
    public function setPacks (Collection $packs): self
    {
        $this->clearPacks();
        foreach ($packs as $pack) {
            $this->addPack($pack);
        }

        return $this;
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

    public function removePack (Pack $pack): self
    {
        if ($this->packs->contains($pack)) {
            $this->packs->removeElement($pack);
            $pack->setCycle(null);
        }

        return $this;
    }

    public function clearPacks (): self
    {
        foreach ($this->getPacks() as $pack) {
            $this->removePack($pack);
        }
        $this->packs->clear();

        return $this;
    }
}
