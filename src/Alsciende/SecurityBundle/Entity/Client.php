<?php

namespace Alsciende\SecurityBundle\Entity;

use FOS\OAuthServerBundle\Entity\Client as BaseClient;
use Doctrine\ORM\Mapping as ORM;

/**
 * Description of Client
 *
 * @author Alsciende <alsciende@icloud.com>
 * 
 * @ORM\Table(name="oauth_clients")
 * @ORM\Entity
 */
class Client extends BaseClient
{

    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * @ORM\Column(type="string",nullable=false)
     */
    protected $name;

    /**
     * @ORM\Column(type="string",nullable=false)
     */
    protected $email;

    /**
     * @ORM\OneToMany(targetEntity="AccessToken", mappedBy="client", cascade={"persist", "remove"})
     * @var AccessToken
     */
    private $accessTokens;
    
    /**
     * @ORM\OneToMany(targetEntity="AuthCode", mappedBy="client", cascade={"persist", "remove"})
     * @var AuthCode
     */
    private $authCodes;
    
    /**
     * @ORM\OneToMany(targetEntity="RefreshToken", mappedBy="client", cascade={"persist", "remove"})
     * @var RefreshToken
     */
    private $refreshTokens;
    
    function getId ()
    {
        return $this->id;
    }

    function getName ()
    {
        return $this->name;
    }

    function getEmail ()
    {
        return $this->email;
    }

    function setId ($id)
    {
        $this->id = $id;
    }

    function setName ($name)
    {
        $this->name = $name;
    }

    function setEmail ($email)
    {
        $this->email = $email;
    }

}
