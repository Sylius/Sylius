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

namespace Sylius\Bundle\UiBundle\Registry;

interface BlockRegistryInterface
{
    /**
     * @return array<string, array<string, Block>>
     */
    public function all(): array;

    /**
     * @param string[] $eventNames
     *
     * @return Block[]
     */
    public function findEnabledForEvents(array $eventNames): array;
}

class_alias(BlockRegistryInterface::class, '\Sylius\Bundle\UiBundle\Registry\TemplateBlockRegistryInterface');
