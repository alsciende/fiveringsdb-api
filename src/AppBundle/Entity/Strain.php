<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JMS\Serializer\Annotation as JMS;

/**
 * A strain of private decks
 *
 * @ORM\Table(name="strains")
 * @ORM\Entity()
 *
 * @JMS\ExclusionPolicy("all")
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Strain
{
  use TimestampableEntity;

    /**
     * Unique identifier of the strain
     *
     * @var string
     * @ORM\Column(name="id", type="string", length=255, unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @JMS\Expose
     * @JMS\Groups({"Default"})
     */
    private $id;

    /**
     * The owner of the strain
     *
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * The last minor deck of the strain
     *
     * @var Deck
     * @ORM\OneToOne(targetEntity="Deck", fetch="EAGER")
     * @ORM\JoinColumn(name="deck_id", referencedColumnName="id")
     * @JMS\Expose
     * @JMS\Groups({"head_group"})
     */
    private $head;

    /**
     * The decks of the strain
     *
     * @var Collection
     * @ORM\OneToMany(targetEntity="Deck", mappedBy="strain", cascade={"remove"})
     * @ORM\JoinColumn(name="strain_id", referencedColumnName="id")
     */
    private $decks;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->decks = new ArrayCollection;
    }

    function getId (): ?string
    {
        return $this->id;
    }

    function setId (string $id): self
    {
        $this->id = $id;

        return $this;
    }

    function getUser (): ?User
    {
        return $this->user;
    }

    function setUser (User $user): self
    {
        $this->user = $user;

        return $this;
    }

    function getHead (): ?Deck
    {
        return $this->head;
    }

    function setHead (Deck $head): self
    {
        $this->head = $head;

        return $this;
    }

    function clearHead (): self
    {
        $this->head = null;

        return $this;
    }

    public function setDecks (Collection $decks): self
    {
        $this->clearDecks();
        foreach ($decks as $deck) {
            $this->addDeck($deck);
        }

        return $this;
    }

    public function addDeck (Deck $deck): self
    {
        if ($this->decks->contains($deck) === false) {
            $this->decks->add($deck);
            $deck->setStrain($this);
        }

        return $this;
    }

    /** @return Collection|Deck[] */
    public function getDecks (): Collection
    {
        return $this->decks;
    }

    public function removeDeck (Deck $deck): self
    {
        if ($this->decks->contains($deck)) {
            $this->decks->removeElement($deck);
            $deck->setStrain(null);
        }

        return $this;
    }

    public function clearDecks (): self
    {
        foreach ($this->getDecks() as $deck) {
            $this->removeDeck($deck);
        }
        $this->decks->clear();

        return $this;
    }
}
