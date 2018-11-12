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

namespace Sylius\Bundle\ThemeBundle\Configuration\Test;

interface TestThemeConfigurationManagerInterface
{
    public function findAll(): array;

    public function add(array $configuration): void;

    public function remove(string $themeName): void;

    /**
     * Clear currently used configurations storage.
     */
    public function clear(): void;
}
