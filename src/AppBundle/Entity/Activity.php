<?php

namespace AppBundle\Entity;
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
    const TYPE_COMMENT_AUTHOR = 1;
    const TYPE_COMMENT_PARTICIPANT = 2;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="integer", nullable=false)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="string", nullable=false)
     */
    private $text;

    /**
     * @var array
     *
     * @ORM\Column(name="route", type="json_array")
     */
    private $route;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var User|null
     * @ORM\ManyToOne(targetEntity="User", inversedBy="activities")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private $user;

    public function __construct (int $type)
    {
        $this->type = $type;
        $this->createdAt = new \DateTime();
        $this->route = [];
    }

    public function getId (): int
    {
        return $this->id;
    }

    public function getType (): int
    {
        return $this->type;
    }

    public function setType (int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getText (): string
    {
        return $this->text;
    }

    public function setText (string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getRoute (): array
    {
        return $this->route;
    }

    public function setRoute (array $route): self
    {
        $this->route = $route;

        return $this;
    }

    public function getCreatedAt (): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt (\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user = null): self
    {
        $this->user = $user;
        if ($user instanceof User) {
            $user->addActivity($this);
        }

        return $this;
    }
}