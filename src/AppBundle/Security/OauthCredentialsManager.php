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
        $data = $this->getUserData($credentials);

        if($data === null) {
            return null;
        }

        return $data['id'];
    }

    public function getUserData(string $credentials): ?array
    {
        $json = $this->getJsonData($credentials);

        $data = json_decode($json, true);

        if(!is_array($data)) {
            return null;
        }

        return $data;
    }

    public function getJsonData(string $credentials): ?string
    {
        $cacheHit = $this->cache->get($credentials);

        if($cacheHit !== null) {
            return $cacheHit;
        }

        $json = $this->oauth->getUserData($credentials);

        if($json !== null) {
            $this->cache->set($credentials, $json);
        }

        return $json;
    }
}
