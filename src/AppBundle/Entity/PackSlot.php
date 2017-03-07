<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Alsciende\DoctrineSerializerBundle\Annotation\Source;

/**
 * Description of PackSlot
 *
 * @ORM\Table(name="pack_slots")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PackSlotRepository")
 * 
 * @Source(break="pack_code")
 * 
 * @author Alsciende <alsciende@icloud.com>
 */
class PackSlot implements \AppBundle\Model\CardSlotInterface
{
    
    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer", nullable=false)
     */
    private $quantity;

    /**
     * @var \AppBundle\Entity\Card
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Card", fetch="EAGER")
     * @ORM\JoinColumn(name="card_code", referencedColumnName="code")
     */
    private $card;

    /**
     * @var \AppBundle\Entity\Pack
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Pack", fetch="EAGER", inversedBy="slots")
     * @ORM\JoinColumn(name="pack_code", referencedColumnName="code")
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

    function getPack ()
    {
        return $this->pack;
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

    
}
