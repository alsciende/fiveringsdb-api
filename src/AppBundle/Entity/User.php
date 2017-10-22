<?php

namespace AppBundle\Entity;

use Alsciende\SerializerBundle\Annotation\Source;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Description of User
 *
 * @ORM\Entity()
 * @ORM\Table(name="users")
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class User implements UserInterface
{

    /**
     * @ORM\Column(name="id", type="string", length=255, unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true)
     *
     * @Source(type="string")
     */
    private $username;

    /**
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     *
     * @var array
     *
     * @ORM\Column(name="roles", type="simple_array")
     */
    private $roles;

    /**
     *
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;

    /**
     * Limit on the size of each strain
     *
     * @var int
     *
     * @ORM\Column(name="strain_size_limit", type="integer")
     */
    private $strainSizeLimit;

    /**
     * Limit on the number of strains
     *
     * @var int
     *
     * @ORM\Column(name="strains_count_limit", type="integer")
     */
    private $strainsCountLimit;

    /**
     * @var Collection|Activity[]
     * @ORM\OneToMany(targetEntity="Activity", mappedBy="user")
     */
    private $activities;

    function __construct ()
    {
        $this->roles = ['ROLE_USER'];
        $this->strainSizeLimit = 10;
        $this->strainsCountLimit = 100;
        $this->activities = new ArrayCollection();
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

    public function getStrainSizeLimit (): ?int
    {
        return $this->strainSizeLimit;
    }

    public function setStrainSizeLimit (int $strainSizeLimit): self
    {
        $this->strainSizeLimit = $strainSizeLimit;

        return $this;
    }

    public function getStrainsCountLimit (): ?int
    {
        return $this->strainsCountLimit;
    }

    public function setStrainsCountLimit (int $strainsCountLimit): self
    {
        $this->strainsCountLimit = $strainsCountLimit;

        return $this;
    }

    /** @return Collection|Activity[] */
    public function getActivities (): Collection
    {
        return $this->activities;
    }

    /** @param Collection|Activity[] $activities */
    public function setActivities (Collection $activities): self
    {
        $this->clearActivitys();
        foreach ($activities as $activity) {
            $this->addActivity($activity);
        }

        return $this;
    }

    public function clearActivitys (): self
    {
        foreach ($this->getActivities() as $activity) {
            $this->removeActivity($activity);
        }
        $this->activities->clear();

        return $this;
    }

    public function removeActivity (Activity $activity): self
    {
        if ($this->activities->contains($activity)) {
            $this->activities->removeElement($activity);
            $activity->setUser(null);
        }

        return $this;
    }

    public function addActivity (Activity $activity): self
    {
        if ($this->activities->contains($activity) === false) {
            $this->activities->add($activity);
            $activity->setUser($this);
        }

        return $this;
    }
}
