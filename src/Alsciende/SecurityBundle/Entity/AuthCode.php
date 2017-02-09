<?php

namespace Alsciende\SecurityBundle\Entity;

use FOS\OAuthServerBundle\Entity\AuthCode as BaseAuthCode;
use Doctrine\ORM\Mapping as ORM;

/**
 * Description of AuthCode
 *
 * @author Alsciende <alsciende@icloud.com>
 * 
 * @ORM\Table(name="oauth_auth_codes")
 * @ORM\Entity
 */
class AuthCode extends BaseAuthCode
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Client",inversedBy="authCodes")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $client;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     */
    protected $user;

}
