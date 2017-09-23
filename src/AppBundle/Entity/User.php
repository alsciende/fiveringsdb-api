<?php

namespace AppBundle\Entity;

use Alsciende\SerializerBundle\Annotation\Source;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Description of User
 *
 * @ORM\Entity
 * @ORM\Table(name="users")
 *
 * @author Alsciende <alsciende@icloud.com>
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessorOrder("alphabetical")
 */
class User implements UserInterface
{

    /**
     * @ORM\Column(name="id", type="string", length=255, unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     *
     * @JMS\Expose
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true)
     *
     * @Source(type="string")
     *
     * @JMS\Expose()
     */
    private $username;

    /**
     *
     * @ORM\Column(name="password",type="string",length=255)
     */
    private $password;

    /**
     *
     * @var array
     *
     * @ORM\Column(name="roles",type="simple_array")
     *
     * @JMS\Expose()
     */
    private $roles;

    /**
     *
     * @var boolean
     *
     * @ORM\Column(name="enabled",type="boolean")
     */
    private $enabled;

    function __construct ()
    {
        $this->roles = ['ROLE_USER'];
    }

    function getId ()
    {
        return $this->id;
    }

    public function setId ($id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getUsername ()
    {
        return $this->username;
    }

    public function setUsername ($username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword ()
    {
        return $this->password;
    }

    public function setPassword ($password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles (): array
    {
        return $this->roles;
    }

    public function setRoles (array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    function addRole (string $role): self
    {
        $roles = $this->roles;
        $roles[] = $role;
        $this->roles = array_unique($roles);

        return $this;
    }

    function hasRole (string $role): bool
    {
        return in_array($role, $this->roles);
    }

    public function isEnabled (): bool
    {
        return $this->enabled;
    }

    public function setEnabled (bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function eraseCredentials ()
    {

    }

    public function getSalt ()
    {
        return '';
    }
}
