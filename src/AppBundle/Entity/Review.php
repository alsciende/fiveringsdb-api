<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Traits\TimestampableEntity;
use Alsciende\SerializerBundle\Annotation\Source;
use JMS\Serializer\Annotation as JMS;

/**
 * A Review written by a User for a Card
 *
 * @ORM\Table(name="reviews")
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessorOrder("alphabetical")
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Review
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
     * @ORM\ManyToOne(targetEntity="Card", inversedBy="reviews")
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

    /**
     * @JMS\VirtualProperty
     */
    function getCardCode (): string
    {
        return $this->card ? $this->card->getCode() : null;
    }

    /**
     * @JMS\VirtualProperty
     */
    function getUserId (): string
    {
        return $this->user ? $this->user->getId() : null;
    }

    public function getText (): string
    {
        return $this->text;
    }

    public function setText (string $text): self
    {
        $this->text = $text;
        return $this;
    }

    public function getCard (): Card
    {
        return $this->card;
    }

    public function setCard (Card $card): self
    {
        $this->card = $card;
        return $this;
    }

    public function getUser (): User
    {
        return $this->user;
    }

    public function setUser (User $user): self
    {
        $this->user = $user;
        return $this;
    }
}
