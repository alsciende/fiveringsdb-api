<?php

namespace AppBundle\Entity;

use AppBundle\Behavior\Entity\FreshnessTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Description of DeckLike
 *
 * @ORM\Entity
 * @ORM\Table(name="deck_likes", indexes={
 *          @ORM\Index(columns={"created_at"})
 *     })
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DeckLike
{
    /**
     * @var \AppBundle\Entity\Deck
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Deck", inversedBy="deckLikes")
     * @ORM\JoinColumn(name="deck_id", referencedColumnName="id")
     */
    private $deck;

    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime",nullable=false)
     */
    private $createdAt;

    function __construct (Deck $deck, User $user)
    {
        $this->deck = $deck;
        $this->user = $user;
    }

    function getDeck (): Deck
    {
        return $this->deck;
    }

    function getUser (): User
    {
        return $this->user;
    }

    function getCreatedAt (): \DateTime
    {
        return $this->createdAt;
    }

    function setDeck (Deck $deck): self
    {
        $this->deck = $deck;

        return $this;
    }

    function setUser (User $user): self
    {
        $this->user = $user;

        return $this;
    }

    function setCreatedAt (\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
