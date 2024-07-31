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

interface TwigEventRendererInterface
{
    /**
     * @param non-empty-list<string> $eventNames
     * @param array<string, mixed> $context
     */
    public function render(array $eventNames, array $context = []): string;
}

class_alias(TwigEventRendererInterface::class, '\Sylius\Bundle\UiBundle\Renderer\TemplateEventRendererInterface');
