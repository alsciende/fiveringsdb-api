<?php

namespace AppBundle\Entity;

use AppBundle\Model\SlotElementInterface;
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

    function getQuantity (): int
    {
        return $this->quantity;
    }

    function getCard (): Card
    {
        return $this->card;
    }

    /**
     * @JMS\VirtualProperty
     */
    function getCardCode (): string
    {
        return $this->card ? $this->card->getCode() : null;
    }


    function getPack (): Pack
    {
        return $this->pack;
    }

    /**
     * @JMS\VirtualProperty
     */
    function getPackCode (): string
    {
        return $this->pack ? $this->pack->getCode() : null;
    }

    function setQuantity ($quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    function setCard (Card $card): self
    {
        $this->card = $card;

        return $this;
    }

    function setPack (Pack $pack): self
    {
        $this->pack = $pack;

        return $this;
    }

    function getElement (): SlotElementInterface
    {
        return $this->card;
    }
}
