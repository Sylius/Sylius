<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Service;

interface SharedStorageInterface
{
    /**
     * @param string $key
     */
    public function get($key);

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has($key);

    /**
     * @param string $key
     */
    public function set($key, $resource);

    public function getLatestResource();

    /**
     * @throws \RuntimeException
     */
    public function setClipboard(array $clipboard);
}
