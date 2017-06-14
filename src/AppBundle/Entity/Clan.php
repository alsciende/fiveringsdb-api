<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Alsciende\SerializerBundle\Annotation\Source;

/**
 * Clan
 *
 * @ORM\Table(name="clans")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ClanRepository")
 *
 * @Source()
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Clan
{
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
     * @var boolean
     *
     * @ORM\Column(name="is_neutral", type="boolean")
     *
     * @Source(type="boolean")
     */
    private $isNeutral;

    /**
     * Get code
     *
     * @return string
     */
    public function getCode (): string
    {
        return $this->code;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Clan
     */
    public function setCode ($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName (): string
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Clan
     */
    public function setName ($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get isNeutral
     *
     * @return bool
     */
    public function getIsNeutral (): bool
    {
        return $this->isNeutral;
    }

    /**
     * Set isNeutral
     *
     * @param bool $isNeutral
     */
    public function setIsNeutral (bool $isNeutral)
    {
        $this->isNeutral = $isNeutral;
    }
}
