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
     * @var array
     *
     * @ORM\Column(name="data", type="json_array")
     */
    private $data;

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
        $this->data = [];
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

    public function getData (): array
    {
        return $this->data;
    }

    public function setData (array $data): self
    {
        $this->data = $data;

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