<?php

namespace Alsciende\SecurityBundle\Entity;

use FOS\OAuthServerBundle\Entity\RefreshToken as BaseRefreshToken;
use Doctrine\ORM\Mapping as ORM;

/**
 * Description of RefreshToken
 *
 * @author Alsciende <alsciende@icloud.com>
 * 
 * @ORM\Table(name="oauth_refresh_tokens")
 * @ORM\Entity
 */
class RefreshToken extends BaseRefreshToken
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Client",inversedBy="refreshTokens")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $client;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     */
    protected $user;

}
