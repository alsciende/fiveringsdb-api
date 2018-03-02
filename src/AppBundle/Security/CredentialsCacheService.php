<?php
/**
 * Created by PhpStorm.
 * User: cedric
 * Date: 22/02/18
 * Time: 10:14
 */

namespace AppBundle\Security;

use Psr\SimpleCache\CacheInterface;

/**
 */
class CredentialsCacheService
{

    /** @var CacheInterface $cache */
    private $cache;

    /** @var int */
    private $ttl;

    /**
     * CredentialsCacheService constructor.
     * @param CacheInterface $cache
     * @param int            $ttl
     */
    public function __construct(CacheInterface $cache, int $ttl)
    {
        $this->cache = $cache;
        $this->ttl = $ttl;
    }

    /**
     * @param string $credentials
     * @return null|string
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function get(string $credentials): ?string
    {
        return $this->cache->get($credentials);
    }

    /**
     * @param string $credentials
     * @param string $json
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function set(string $credentials, string $json)
    {
        $this->cache->set($credentials, $json, $this->ttl);
    }

    /**
     *
     */
    public function clear()
    {
        $this->cache->clear();
    }
}
