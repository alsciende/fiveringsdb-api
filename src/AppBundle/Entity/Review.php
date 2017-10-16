<?php

namespace AppBundle\Entity;

use Alsciende\SerializerBundle\Annotation\Source;
use AppBundle\Behavior\Entity\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * A Review written by a User for a Card
 *
 * @ORM\Table(name="reviews")
 * @ORM\Entity
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Review implements Timestampable
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
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", nullable=false)
     *
     * @Source(type="string")
     */
    private $text;

    /**
     * @var Card
     *
     * @ORM\ManyToOne(targetEntity="Card", inversedBy="reviews")
     * @ORM\JoinColumn(name="card_id", referencedColumnName="id")
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

    public function getText (): ?string
    {
        return $this->text;
    }

    public function setText (string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getCard (): ?Card
    {
        return $this->card;
    }

    public function setCard (Card $card): self
    {
        $this->card = $card;

        return $this;
    }

    public function getUser (): ?User
    {
        return $this->user;
    }

    public function setUser (User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
