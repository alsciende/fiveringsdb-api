<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Alsciende\DoctrineSerializerBundle\Annotation\Source;

/**
 * Clan
 *
 * @ORM\Table(name="clans")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ClanRepository")
 * 
 * @Source(multipleFiles=false,multipleRecords=true,groupBy=null)
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
     * @return Clan
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
