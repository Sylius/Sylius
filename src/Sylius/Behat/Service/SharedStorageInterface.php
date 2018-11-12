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
    public function get(string $key);

    public function has(string $key): bool;

    public function set(string $key, $resource);

    public function getLatestResource();

    /**
     * @throws \RuntimeException
     */
    public function setClipboard(array $clipboard);
}
