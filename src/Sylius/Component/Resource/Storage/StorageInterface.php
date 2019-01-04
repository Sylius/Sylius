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

interface StorageInterface
{
    public function has(string $name): bool;

    public function get(string $name, $default = null);

    public function set(string $name, $value): void;

    public function remove(string $name): void;

    public function all(): array;
}
