<?php

declare(strict_types=1);

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JMS\Serializer\Annotation as JMS;

/**
 * A Comment written by a User for a Deck
 *
 * @ORM\Table(name="comments")
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessorOrder("alphabetical")
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Comment
{
    use TimestampableEntity;

    /**
     * @var string
     *
     * @ORM\Column(name="id", type="integer", unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @JMS\Expose
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", nullable=false)
     *
     * @JMS\Expose
     */
    private $text;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=false)
     *
     * @JMS\Expose()
     */
    private $visible;

    /**
     * @var Deck
     *
     * @ORM\ManyToOne(targetEntity="Deck", inversedBy="comments")
     * @ORM\JoinColumn(name="deck_id", referencedColumnName="id")
     *
     * @JMS\Expose()
     * @JMS\Groups({"deck_group"})
     */
    private $deck;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *
     * @JMS\Expose()
     * @JMS\Groups({"user_group"})
     */
    private $user;

    public function __construct ()
    {
        $this->visible = true;
    }

    public function setText (string $text): self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @JMS\VirtualProperty
     */
    function getDeckId (): ?string
    {
        return $this->deck ? $this->deck->getId() : null;
    }

    /**
     * @JMS\VirtualProperty
     */
    function getUserId (): ?string
    {
        return $this->user ? $this->user->getId() : null;
    }

    public function setDeck (Deck $deck): self
    {
        $this->deck = $deck;

        return $this;
    }

    public function setUser (User $user): self
    {
        $this->user = $user;

        return $this;
    }

    function getId ()
    {
        return $this->id;
    }

    public function getText (): ?string
    {
        return $this->text;
    }

    public function getDeck (): ?Deck
    {
        return $this->deck;
    }

    public function getUser (): ?User
    {
        return $this->user;
    }

    public function isVisible (): bool
    {
        return $this->visible;
    }

    public function setVisible (bool $visible): self
    {
        $this->visible = $visible;

        return $this;
    }
}
