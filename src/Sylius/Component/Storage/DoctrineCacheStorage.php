<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Storage;

use Doctrine\Common\Cache\Cache;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class DoctrineCacheStorage implements StorageInterface
{
    /**
     * @var Cache
     */
    protected $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function hasData($key)
    {
        return $this->cache->contains($key);
    }

    /**
     * {@inheritdoc}
     */
    public function getData($key, $default = null)
    {
        $data = $this->cache->fetch($key);
        if ($data) {
            return $data;
        }

        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function setData($key, $value)
    {
        $this->cache->save($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function removeData($key)
    {
        $this->cache->delete($key);
    }
}
