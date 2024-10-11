<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\UiBundle\Storage;

trigger_deprecation(
    'sylius/ui-bundle',
    '1.14',
    'The "%s" class is deprecated and will be removed in Sylius 2.0. Use Sylius\Bundle\GridBundle\Storage\FilterStorageInterface from sylius/grid-bundle version 1.13 or later instead.',
    FilterStorage::class,
);
interface FilterStorageInterface
{
    public function set(array $filters): void;

    public function all(): array;

    public function hasFilters(): bool;
}
