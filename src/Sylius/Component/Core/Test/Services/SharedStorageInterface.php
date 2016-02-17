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

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface SharedStorageInterface
{
    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get($key);

    /**
     * @param string $key
     * @param mixed $resource
     */
    public function set($key, $resource);

    /**
     * @return mixed
     */
    public function getLatestResource();

    /**
     * @param array $clipboard
     *
     * @throws \RuntimeException
     */
    public function setClipboard(array $clipboard);
}
