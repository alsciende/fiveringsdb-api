<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Alsciende\SerializerBundle\Annotation\Source;
use JMS\Serializer\Annotation as JMS;

/**
 * Description of PackCard
 *
 * @ORM\Table(name="pack_cards")
 * @ORM\Entity
 *
 * @Source(break="pack_code")
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessorOrder("alphabetical")
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class PackCard implements \AppBundle\Model\CardSlotInterface
{

    use TimestampableEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer", nullable=false)
     *
     * @Source(type="integer")
     *
     * @JMS\Expose
     */
    private $quantity;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer", nullable=false)
     *
     * @Source(type="integer")
     *
     * @JMS\Expose
     */
    private $position;

    /**
     * @var \AppBundle\Entity\Card
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Card", inversedBy="packCards")
     * @ORM\JoinColumn(name="card_code", referencedColumnName="code")
     *
     * @Source(type="association")
     */
    private $card;

    /**
     * @var \AppBundle\Entity\Pack
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Pack", inversedBy="cards")
     * @ORM\JoinColumn(name="pack_code", referencedColumnName="code")
     *
     * @Source(type="association")
     */
    private $pack;

    function getQuantity ()
    {
        return $this->quantity;
    }

    function getCard ()
    {
        return $this->card;
    }

    /**
     * @JMS\VirtualProperty
     * @return string
     */
    function getCardCode ()
    {
        return $this->card ? $this->card->getCode() : null;
    }


    function getPack ()
    {
        return $this->pack;
    }

    /**
     * @JMS\VirtualProperty
     * @return string
     */
    function getPackCode ()
    {
        return $this->pack ? $this->pack->getCode() : null;
    }

    function setQuantity ($quantity)
    {
        $this->quantity = $quantity;
    }

    function setCard (\AppBundle\Entity\Card $card)
    {
        $this->card = $card;
    }

    function setPack (\AppBundle\Entity\Pack $pack)
    {
        $this->pack = $pack;
    }

    function getElement ()
    {
        return $this->card;
    }
}
