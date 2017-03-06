<?php

namespace Tests\AlsciendeSecurityBundle;

use Alsciende\SecurityBundle\Service\UserManager;
use FOS\OAuthServerBundle\Model\AccessTokenManagerInterface;
use FOS\OAuthServerBundle\Model\ClientManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Description of AuthTest
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class AuthTest extends KernelTestCase
{

    use DomainFixtures;

    /** @var \Doctrine\ORM\EntityManager */
    private $em;

    /** @var AccessTokenManagerInterface */
    private $accessTokenManager;
    
    /** @var ClientManagerInterface */
    private $clientManager;
    
    /** @var UserManager */
    private $userManager;
    
    protected function setUp ()
    {
        self::bootKernel();
        
        $this->em = static::$kernel->getContainer()
                ->get('doctrine')
                ->getManager();

        $this->accessTokenManager = static::$kernel->getContainer()
                ->get('fos_oauth_server.access_token_manager');

        $this->clientManager = static::$kernel->getContainer()
                ->get('fos_oauth_server.client_manager');
        
        $this->userManager = static::$kernel->getContainer()
                ->get('alsciende_security.user_manager');
        
        $this->clearDatabase();

    }

    public function testCreateClient ()
    {
        $client = $this->createClient($this->clientManager);
        $this->assertNotNull($client);
    }

    public function testCreateUser ()
    {
        $user = $this->createUser($this->userManager);
        $this->assertNotNull($user);
    }

    public function testCreateToken ()
    {
        $client = $this->createClient($this->clientManager);
        $user = $this->createUser($this->userManager);
        $token = $this->createAccessToken($this->accessTokenManager, $client, $user);
        $this->assertNotNull($token);
    }

    protected function tearDown ()
    {
        parent::tearDown();

        $this->accessTokenManager = null;
        $this->clientManager = null;
        $this->userManager = null;
    }

}
