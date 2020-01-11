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

namespace Sylius\Bundle\UiBundle\Registry;

interface TemplateBlockRegistryInterface
{
    /**
     * @return TemplateBlock[][]
     *
     * @psalm-return array<string, list<TemplateBlock>>
     */
    public function all(): array;

    /**
     * @return TemplateBlock[]
     *
     * @psalm-return list<TemplateBlock>
     */
    public function findEnabledForEvent(string $eventName): array;
}
