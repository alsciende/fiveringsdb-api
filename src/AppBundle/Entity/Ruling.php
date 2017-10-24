<?php

namespace AppBundle\Entity;

use AppBundle\Behavior\Entity\SequentialIdTrait;
use AppBundle\Behavior\Entity\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * A Ruling written by a User for a Card
 *
 * @ORM\Entity()
 * @ORM\Table(name="rulings")
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Ruling implements Timestampable
{
    use TimestampableEntity;
    use SequentialIdTrait;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", nullable=false)
     */
    private $text;

    /**
     * @var string
     *
     * @ORM\Column(name="source", type="text", nullable=false)
     */
    private $source;

    /**
     * @var string|null
     *
     * @ORM\Column(name="link", type="text", nullable=true)
     */
    private $link;

    /**
     * @var Card
     *
     * @ORM\ManyToOne(targetEntity="Card", inversedBy="rulings")
     * @ORM\JoinColumn(name="card_id", referencedColumnName="id")
     */
    private $card;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    public function getText (): ?string
    {
        return $this->text;
    }

    public function setText (string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getSource (): ?string
    {
        return $this->source;
    }

    public function setSource (string $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getLink (): ?string
    {
        return $this->link;
    }

    public function setLink (string $link = null): self
    {
        $this->link = $link;

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
