<?php

namespace Tests\AlsciendeSecurityBundle;

use Alsciende\SecurityBundle\Entity\AccessToken;
use Alsciende\SecurityBundle\Entity\Client;
use Alsciende\SecurityBundle\Service\UserManager;
use AppBundle\Entity\User;
use FOS\OAuthServerBundle\Model\AccessTokenManagerInterface;
use FOS\OAuthServerBundle\Model\ClientInterface;
use FOS\OAuthServerBundle\Model\ClientManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Description of DomainFixtures
 *
 * @author Alsciende <alsciende@icloud.com>
 */
trait DomainFixtures
{

    function clearDatabase ()
    {

        $classNames = [
            Client::class,
            User::class,
            AccessToken::class
        ];

        foreach($classNames as $className) {
            foreach($this->em->getRepository($className)->findAll() as $entity) {
                $this->em->remove($entity);
            }
        }

        $this->em->flush();
    }

    function createClient (ClientManagerInterface $clientManager)
    {
        $client = $clientManager->createClient();
        $client->setRedirectUris(array('http://localhost/redirect'));
        $client->setAllowedGrantTypes(array('authorization_code', 'refresh_tokens'));
        $client->setName("Test Client");
        $client->setEmail("test-client@oauth.net");
        $clientManager->updateClient($client);
        return $client;
    }

    function createUser (UserManager $userManager)
    {
        $user = $userManager->createUser("Test User", "testtest");
        $userManager->updateUser($user);
        return $user;
    }

    function createAccessToken (AccessTokenManagerInterface $accessTokenManager, ClientInterface $client, UserInterface $user)
    {
        $token = $accessTokenManager->createToken();
        $token->setToken("TEST_TOKEN");
        $token->setClient($client);
        $token->setExpiresAt(null);
        $token->setScope(null);
        $token->setUser($user);

        $accessTokenManager->updateToken($token);
        return $token;
    }

}
