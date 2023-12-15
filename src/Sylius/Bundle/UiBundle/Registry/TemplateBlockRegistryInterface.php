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

interface TemplateBlockRegistryInterface
{
    /**
     * @return array<string, array<string, TemplateBlock>>
     */
    public function all(): array;

    /**
     * @param non-empty-list<string> $eventNames
     *
     * @return TemplateBlock[]
     */
    public function findEnabledForEvents(array $eventNames): array;
}
