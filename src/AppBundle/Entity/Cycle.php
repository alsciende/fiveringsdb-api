<?php

namespace AppBundle\Entity;

use AppBundle\Repository\CycleRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Alsciende\SerializerBundle\Annotation\Source;

/**
 * Cycle
 *
 * @ORM\Table(name="cycles")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CycleRepository")
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

    public function getCode (): string
    {
        return $this->code;
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
}
