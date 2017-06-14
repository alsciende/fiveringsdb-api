<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Description of DeckLike
 *
 * @ORM\Table(name="deck_likes")
 * @ORM\Entity
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DeckLike
{

    /**
     * @var \AppBundle\Entity\Deck
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Deck")
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

    function __construct (\AppBundle\Entity\Deck $deck, \AppBundle\Entity\User $user)
    {
        $this->deck = $deck;
        $this->user = $user;
    }

    /**
     *
     * @return \AppBundle\Entity\Deck
     */
    function getDeck (): \AppBundle\Entity\Deck
    {
        return $this->deck;
    }

    /**
     *
     * @return \AppBundle\Entity\User
     */
    function getUser (): \AppBundle\Entity\User
    {
        return $this->user;
    }

    /**
     *
     * @return \DateTime
     */
    function getCreatedAt (): \DateTime
    {
        return $this->createdAt;
    }

    /**
     *
     * @param \AppBundle\Entity\Deck $deck
     * @return DeckLike
     */
    function setDeck (\AppBundle\Entity\Deck $deck)
    {
        $this->deck = $deck;
        return $this;
    }

    /**
     *
     * @param \AppBundle\Entity\User $user
     * @return DeckLike
     */
    function setUser (\AppBundle\Entity\User $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     *
     * @param \DateTime $createdAt
     * @return DeckLike
     */
    function setCreatedAt (\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }


}
