<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Service;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class SharedStorage implements SharedStorageInterface
{
    /**
     * @var array
     */
    private $clipboard = [];

    /**
     * @var string|null
     */
    private $latestKey;

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        if (!isset($this->clipboard[$key])) {
            throw new \InvalidArgumentException(sprintf('There is no current resource for "%s"!', $key));
        }

        return $this->clipboard[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return isset($this->clipboard[$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $resource)
    {
        $this->clipboard[$key] = $resource;
        $this->latestKey = $key;
    }

    /**
     * {@inheritdoc}
     */
    public function getLatestResource()
    {
        if (!isset($this->clipboard[$this->latestKey])) {
            throw new \InvalidArgumentException(sprintf('There is no latest resource!', $this->latestKey));
        }

        return $this->clipboard[$this->latestKey];
    }

    /**
     * {@inheritdoc}
     */
    public function setClipboard(array $clipboard)
    {
        $this->clipboard = $clipboard;
    }
}
