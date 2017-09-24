<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @JMS\Expose
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", nullable=false)
     *
     * @JMS\Expose
     *
     * @Assert\NotBlank()
     */
    private $text;

    /**
     * @var string
     *
     * @ORM\Column(name="source", type="text", nullable=false)
     *
     * @JMS\Expose
     *
     * @Assert\NotBlank()
     */
    private $source;

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="text", nullable=true)
     *
     * @JMS\Expose
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
