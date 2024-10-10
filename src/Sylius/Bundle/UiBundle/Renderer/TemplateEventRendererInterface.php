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

namespace Sylius\Bundle\UiBundle\Renderer;

trigger_deprecation(
    'sylius/ui-bundle',
    '1.14',
    'The "%s" class is deprecated and will be removed in Sylius 2.0',
    TemplateEventRendererInterface::class,
);
interface TemplateEventRendererInterface
{
    /**
     * @param non-empty-list<string> $eventNames
     */
    public function render(array $eventNames, array $context = []): string;
}
