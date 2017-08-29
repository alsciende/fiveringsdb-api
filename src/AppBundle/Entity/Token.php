<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Table(name="tokens")
 * @ORM\Entity()
 *
 * @Serializer\ExclusionPolicy("all")
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Token
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", length=255, unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     *
     * @Serializer\Expose()
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *
     * @Serializer\Expose()
     * @Serializer\Groups({"user_group"})
     */
    private $user;

    public function getId (): ?string
    {
        return $this->id;
    }

    public function setId (string $id): self
    {
        $this->id = $id;

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