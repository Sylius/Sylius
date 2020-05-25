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

namespace Sylius\Bundle\UiBundle\Renderer;

/**
 * @experimental
 */
interface TemplateEventRendererInterface
{
    /**
     * @param string[] $eventNames
     * @psalm-param non-empty-list<string> $eventNames
     */
    public function render(array $eventNames, array $context = []): string;
}
