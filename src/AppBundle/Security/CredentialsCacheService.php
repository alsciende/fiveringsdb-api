<?php
/**
 * Created by PhpStorm.
 * User: cedric
 * Date: 22/02/18
 * Time: 10:14
 */

namespace AppBundle\Security;

use Predis\Client;
use Psr\SimpleCache\CacheInterface;

use Symfony\Component\Cache\Simple\RedisCache;

/**
 */
class CredentialsCacheService
{
    /** @var CacheInterface $cache */
    private $cache;

    /** @var int $ttl */
    private $ttl;

    public function __construct(string $url, int $ttl)
    {
        $this->cache = new RedisCache(new Client(
            $url,
            [
                'timeout' => 1,
            ]
        ));

        $this->ttl = $ttl;
    }

    public function has(string $credentials): bool
    {
        return $this->cache->has($credentials);
    }

    public function get(string $credentials): string
    {
        return $this->cache->get($credentials);
    }

    public function set(string $credentials, string $userId)
    {
        $this->cache->set($credentials, $userId, $this->ttl);
    }

    public function clear()
    {
        $this->cache->clear();
    }
}
