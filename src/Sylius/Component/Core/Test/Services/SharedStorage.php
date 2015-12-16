<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Test\Services;

use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class SharedStorage implements SharedStorageInterface
{
    /**
     * @var array
     */
    private $clipboard = array();

    /**
     * @var string|null
     */
    private $latestKey = null;

    /**
     * {@inheritdoc}
     */
    public function setCurrentResource($key, ResourceInterface $resource, $override = false)
    {
        if (isset($this->clipboard[$key]) && !$override) {
            throw new \RuntimeException('This key is already used, if you want override set override flag');
        }

        $this->clipboard[$key] = $resource;
        $this->latestKey = $key;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentResource($key)
    {
        if (!isset($this->clipboard[$key])) {
            throw new \InvalidArgumentException(sprintf('There is no current resource for "%s"!', $key));
        }

        return $this->clipboard[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function getLatestResource()
    {
        return $this->clipboard[$this->latestKey];
    }

    /**
     * {@inheritdoc}
     */
    public function setClipboard(array $clipboard, $override = false)
    {
        if (!empty($this->clipboard) && !$override) {
            throw new \RuntimeException('Clipboard is not empty, if you want override set override flag');
        }

        $this->clipboard = $clipboard;
    }
}
