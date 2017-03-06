<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Description of User
 * 
 * @ORM\Entity
 * @ORM\Table(name="users")
 * 
 * @author Alsciende <alsciende@icloud.com>
 */
class User implements UserInterface
{

    /**
     * @ORM\Column(name="id", type="string", length=255, unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private $username;

    /**
     *
     * @ORM\Column(name="password",type="string",length=255)
     */
    private $password;

    function getId ()
    {
        return $this->id;
    }

    public function getUsername ()
    {
        return $this->username;
    }

    public function getRoles ()
    {
        return array('ROLE_USER');
    }

    public function getPassword ()
    {
        return $this->password;
    }

    public function getSalt ()
    {
        
    }

    public function eraseCredentials ()
    {
        
    }

    function setUsername ($username)
    {
        $this->username = $username;
    }

    function setPassword ($password)
    {
        $this->password = $password;
    }

}
