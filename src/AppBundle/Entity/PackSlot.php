<?php

namespace AppBundle\Entity;

use AppBundle\Model\SlotInterface;
use AppBundle\Repository\PackSlotRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * PackSlot
 *
 * @ORM\Table(name="pack_slot")
 * @ORM\Entity(repositoryClass="PackSlotRepository")
 * 
 * @author Alsciende <alsciende@icloud.com>
 */
class PackSlot implements SlotInterface
{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="smallint")
     */
    private $quantity;

    /**
     * @var Card
     *
     * @ORM\ManyToOne(targetEntity="Card", fetch="EAGER")
     */
    private $card;

    /**
     * @var Pack
     *
     * @ORM\ManyToOne(targetEntity="Pack", fetch="EAGER")
     */
    private $pack;

    /**
     * Get id
     *
     * @return int
     */
    public function getId ()
    {
        return $this->id;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return PackSlot
     */
    public function setQuantity ($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return int
     */
    public function getQuantity ()
    {
        return $this->quantity;
    }

    /**
     * Get card
     * 
     * @return Card
     */
    function getCard ()
    {
        return $this->card;
    }

    /**
     * Get pack
     * 
     * @return Pack
     */
    function getPack ()
    {
        return $this->pack;
    }

    /**
     * Set card
     * 
     * @param Card $card
     * @return PackSlot
     */
    function setCard (Card $card)
    {
        $this->card = $card;

        return $this;
    }

    /**
     * Set pack
     * 
     * @param Pack $pack
     * @return PackSlot
     */
    function setPack (Pack $pack)
    {
        $this->pack = $pack;

        return $this;
    }

}
