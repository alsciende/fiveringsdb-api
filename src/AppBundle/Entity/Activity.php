<?php

namespace AppBundle\Entity;

use AppBundle\Behavior\Entity\SequentialIdTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Description of Activity
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ActivityRepository")
 * @ORM\Table(name="activities")
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Activity
{
    const TYPE_COMMENT_ADDED = 1;
    const TYPE_DECKLIST_PUBLISHED = 2;
    const TYPE_DECKLIST_SHARED = 3;

    use SequentialIdTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="integer", nullable=false)
     */
    private $type;

    /**
     * @var Deck
     * @ORM\ManyToOne(targetEntity="Deck", inversedBy="activities", fetch="EAGER")
     * @ORM\JoinColumn(name="deck_id", referencedColumnName="id", nullable=false)
     */
    private $deck;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", fetch="EAGER")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var Collection|Notification[]
     * @ORM\OneToMany(targetEntity="Notification", mappedBy="activity", cascade={"persist","remove"})
     */
    private $notifications;

    public function __construct (int $type, Deck $deck, User $user)
    {
        $this->type = $type;
        $this->deck = $deck;
        $this->user = $user;
        $this->createdAt = new \DateTime();
        $this->notifications = new ArrayCollection();
    }

    public function getType (): int
    {
        return $this->type;
    }

    public function getDeck(): Deck
    {
        return $this->deck;
    }

    public function getUser (): User
    {
        return $this->user;
    }

    public function getCreatedAt (): \DateTime
    {
        return $this->createdAt;
    }

    /** @return Collection|Notification[] */
    public function getNotifications (): Collection
    {
        return $this->notifications;
    }

    /** @param Collection|Notification[] $notifications */
    public function setNotifications (Collection $notifications): self
    {
        $this->clearNotifications();
        foreach ($notifications as $notification) {
            $this->addNotification($notification);
        }

        return $this;
    }

    public function clearNotifications (): self
    {
        foreach ($this->getNotifications() as $notification) {
            $this->removeNotification($notification);
        }
        $this->notifications->clear();

        return $this;
    }

    public function removeNotification (Notification $notification): self
    {
        if ($this->notifications->contains($notification)) {
            $this->notifications->removeElement($notification);
        }

        return $this;
    }

    public function addNotification (Notification $notification): self
    {
        if ($this->notifications->contains($notification) === false) {
            $this->notifications->add($notification);
        }

        return $this;
    }
}