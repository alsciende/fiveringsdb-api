<?php

namespace AppBundle\Entity;

use AppBundle\Traits\TimestampableEntity;
use Doctrine\ORM\Mapping as ORM;
use Alsciende\SerializerBundle\Annotation\Source;
use JMS\Serializer\Annotation as JMS;

/**
 * Element
 *
 * @ORM\Table(name="elements")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ElementRepository")
 *
 * @Source()
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessorOrder("alphabetical")
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Element
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
     *
     * @JMS\Expose
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     *
     * @Source(type="string")
     *
     * @JMS\Expose
     */
    private $name;

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Element
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
     * @return Element
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

}
