<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Traits\TimestampableEntity;
use Alsciende\SerializerBundle\Annotation\Source;
use JMS\Serializer\Annotation as JMS;

/**
 * A Ruling written by a User for a Card
 *
 * @ORM\Table(name="rulings")
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessorOrder("alphabetical")
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Ruling
{
    use TimestampableEntity;

    /**
     * @var string
     *
     * @ORM\Column(name="id", type="integer", unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Source(type="integer")
     *
     * @JMS\Expose
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", nullable=false)
     *
     * @Source(type="string")
     *
     * @JMS\Expose
     */
    private $text;

    /**
     * @var Card
     *
     * @ORM\ManyToOne(targetEntity="Card", inversedBy="rulings")
     * @ORM\JoinColumn(name="card_code", referencedColumnName="code")
     *
     * @Source(type="association")
     */
    private $card;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *
     * @Source(type="association")
     */
    private $user;

    function getId ()
    {
        return $this->id;
    }

    function getText ()
    {
        return $this->text;
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

    function getUser ()
    {
        return $this->user;
    }

    /**
     * @JMS\VirtualProperty
     * @return string
     */
    function getUserId ()
    {
        return $this->user ? $this->user->getId() : null;
    }

    function setText ($text)
    {
        $this->text = $text;

        return $this;
    }

    function setCard (Card $card)
    {
        $this->card = $card;

        return $this;
    }

    function setUser (User $user)
    {
        $this->user = $user;

        return $this;
    }


}
