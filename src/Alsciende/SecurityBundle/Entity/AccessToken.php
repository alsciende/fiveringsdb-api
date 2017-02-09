<?php

namespace Alsciende\SecurityBundle\Entity;

use FOS\OAuthServerBundle\Entity\AccessToken as BaseAccessToken;
use Doctrine\ORM\Mapping as ORM;

/**
 * Description of AccessToken
 *
 * @author Alsciende <alsciende@icloud.com>
 * 
 * @ORM\Table(name="oauth_access_tokens")
 * @ORM\Entity
 */
class AccessToken extends BaseAccessToken
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Client",inversedBy="accessTokens")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $client;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     */
    protected $user;

}
