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

namespace Sylius\Component\Resource\Storage;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
interface StorageInterface
{
    /**
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name): bool;

    /**
     * @param string $name
     * @param mixed $default
     *
     * @return mixed
     */
    public function get(string $name, $default = null);

    /**
     * @param string $name
     * @param mixed $value
     */
    public function set(string $name, $value): void;

    /**
     * @param string $name
     */
    public function remove(string $name): void;

    /**
     * @return array
     */
    public function all(): array;
}
