<?php
/**
 * Created by PhpStorm.
 * User: cedric
 * Date: 21/02/18
 * Time: 17:35
 */

namespace AppBundle\Security;

use AppBundle\Behavior\Service\OauthServiceInterface;

/**
 */
class OauthCredentialsManager
{
    /** @var OauthServiceInterface $oauth */
    private $oauth;

    /** @var CredentialsCacheService $cache */
    private $cache;

    public function __construct(OauthServiceInterface $oauth, CredentialsCacheService $cache)
    {
        $this->oauth = $oauth;
        $this->cache = $cache;
    }

    public function getUserId(string $credentials): ?string
    {
        if($this->cache->has($credentials)) {
            return $this->cache->get($credentials);
        }

        $data = $this->oauth->getUserData($credentials);

        if($data === null) {
            return null;
        }

        $userId = $data['id'];

        $this->cache->set($credentials, $userId);

        return $userId;
//        $user = $this->userManager->findUserById($data['id']);
//
//        if(!$user instanceof UserManager) {
//            $user = $this->userManager->createUser($data['id'], $data['username']);
//            $this->userManager->updateUser($user);
//        }
    }
}
