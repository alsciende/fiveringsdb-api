<?php

namespace AppBundle\Entity;

use AppBundle\Behavior\Entity\Timestampable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * A strain of private decks
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\StrainRepository")
 * @ORM\Table(name="strains")
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Strain implements Timestampable
{
    use TimestampableEntity;

    /**
     * Unique identifier of the strain
     *
     * @var string
     *
     * @ORM\Column(name="id", type="string", length=255, unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @var string
     */
    private $origin;

    /**
     * The owner of the strain
     *
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", fetch="EAGER")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * The last minor deck of the strain
     *
     * @var Deck|null
     *
     * @ORM\OneToOne(targetEntity="Deck", fetch="EAGER", cascade={"persist"})
     * @ORM\JoinColumn(name="deck_id", referencedColumnName="id")
     */
    private $head;

    /**
     * The decks of the strain
     *
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Deck", mappedBy="strain", cascade={"remove"})
     * @ORM\JoinColumn(name="strain_id", referencedColumnName="id")
     * @ORM\OrderBy({"createdAt" = "ASC"})
     */
    private $decks;

    public function __construct (User $user)
    {
        $this->user = $user;
        $this->decks = new ArrayCollection;
    }

    public function getId (): ?string
    {
        return $this->id;
    }

    public function setId (string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getOrigin (): ?string
    {
        return $this->origin;
    }

    public function setOrigin (string $origin): self
    {
        $this->origin = $origin;

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

    public function getHead (): ?Deck
    {
        return $this->head;
    }

    public function setHead (Deck $head): self
    {
        $this->head = $head;

        return $this;
    }

    public function clearHead (): self
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
