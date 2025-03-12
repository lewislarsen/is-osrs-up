<?php

namespace App\Services;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class CacheService
{
    private FilesystemAdapter $cache;

    public function __construct(FilesystemAdapter $cache)
    {
        $this->cache = $cache;
    }

    public function get(string $key)
    {
        $cacheItem = $this->cache->getItem($key);

        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        return null;
    }

    public function set(string $key, $value, int $ttl = 3600): void
    {
        $cacheItem = $this->cache->getItem($key);
        $cacheItem->set($value);
        $cacheItem->expiresAfter($ttl);

        $this->cache->save($cacheItem);
    }

    public function delete(string $key): void
    {
        $this->cache->deleteItem($key);
    }

    public function clear(): void
    {
        $this->cache->clear();
    }
}