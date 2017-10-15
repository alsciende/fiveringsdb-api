<?php

namespace AppBundle\Entity;

use AppBundle\Behavior\Entity\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * Feature is when a deck is featured on the front page
 *
 * @author Alsciende <alsciende@icloud.com>
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FeatureRepository")
 * @ORM\Table(name="features", indexes={
 *          @ORM\Index(columns={"date"})
 *     })
 */
class Feature implements Timestampable
{
    use TimestampableEntity;

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $clan;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date", nullable=false)
     */
    private $date;

    /**
     * @var Deck
     *
     * @ORM\OneToOne(targetEntity="Deck", inversedBy="feature")
     * @ORM\JoinColumn(name="deck_id", referencedColumnName="id")
     */
    private $deck;

    public function __construct (string $clan, \DateTime $date, Deck $deck)
    {
        $this->clan = $clan;
        $this->date = $date;
        $this->deck = $deck;
    }

    public function getId (): int
    {
        return $this->id;
    }

    public function getClan (): string
    {
        return $this->clan;
    }

    public function setClan (string $clan): self
    {
        $this->clan = $clan;

        return $this;
    }

    public function getDate (): \DateTime
    {
        return $this->date;
    }

    public function setDate (\DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getDeck (): Deck
    {
        return $this->deck;
    }

    public function setDeck (Deck $deck): self
    {
        $this->deck = $deck;

        return $this;
    }
}