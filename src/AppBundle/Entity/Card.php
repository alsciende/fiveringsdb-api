<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Alsciende\SerializerBundle\Annotation\Source;

/**
 * Card
 *
 * @ORM\Table(name="cards")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CardRepository")
 * 
 * @Source(break="code")
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Card
{

    use TimestampableEntity;

    /**
     * @var string
     * 
     * @Assert\NotBlank()
     * @Assert\Length(min=5,max=5)
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
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="name", type="string", length=255)
     * 
     * @Groups({"alsciende_serializer"})
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="cost", type="integer", nullable=true)
     * 
     * @Groups({"alsciende_serializer"})
     */
    private $cost;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", nullable=true)
     * 
     * @Groups({"alsciende_serializer"})
     */
    private $text;

    /**
     * @var \AppBundle\Entity\Type
     *
     * @ORM\ManyToOne(targetEntity="Type", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="type_code", referencedColumnName="code")
     */
    private $type;

    /**
     * @var \AppBundle\Entity\Clan
     *
     * @ORM\ManyToOne(targetEntity="Clan", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="clan_code", referencedColumnName="code")
     */
    private $clan;

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Card
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
     * @return Card
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
     * Set cost
     *
     * @param integer $cost
     *
     * @return Card
     */
    public function setCost ($cost)
    {
        $this->cost = $cost;

        return $this;
    }

    /**
     * Get cost
     *
     * @return int
     */
    public function getCost ()
    {
        return $this->cost;
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return Card
     */
    public function setText ($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText ()
    {
        return $this->text;
    }

    /**
     * Get type
     * 
     * @return Type
     */
    function getType ()
    {
        return $this->type;
    }

    /**
     * Get clan
     * 
     * @return Clan
     */
    function getClan ()
    {
        return $this->clan;
    }

    /**
     * Set type
     * 
     * @param \AppBundle\Entity\Type $type
     *
     * @return Card
     */
    function setType (\AppBundle\Entity\Type $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Set clan
     * 
     * @param \AppBundle\Entity\Clan $clan
     *
     * @return Card
     */
    function setClan (\AppBundle\Entity\Clan $clan)
    {
        $this->clan = $clan;

        return $this;
    }

}
