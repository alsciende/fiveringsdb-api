<?php

declare(strict_types=1);

namespace AppBundle\Entity;

use AppBundle\Behavior\Entity\FreshnessTrait;
use AppBundle\Behavior\Entity\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * A Comment written by a User for a Deck
 *
 * @ORM\Entity
 * @ORM\Table(name="comments", indexes={
 *          @ORM\Index(columns={"created_at"})
 *     })
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Comment implements Timestampable
{
    use TimestampableEntity;

    /**
     * @var string
     *
     * @ORM\Column(name="id", type="integer", unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", nullable=false)
     */
    private $text;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=false)
     */
    private $visible;

    /**
     * @var Deck
     *
     * @ORM\ManyToOne(targetEntity="Deck", inversedBy="comments")
     * @ORM\JoinColumn(name="deck_id", referencedColumnName="id")
     */
    private $deck;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    public function __construct ()
    {
        $this->text = 'Default text';
        $this->visible = true;
    }

    public function setText (string $text): self
    {
        $this->text = $text;

        return $this;
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

    public function getText (): string
    {
        return $this->text;
    }

    public function getDeck (): Deck
    {
        return $this->deck;
    }

    public function getUser (): User
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
