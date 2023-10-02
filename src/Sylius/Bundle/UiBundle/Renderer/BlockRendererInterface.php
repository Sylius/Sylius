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

use Sylius\Bundle\UiBundle\Registry\Block;

interface BlockRendererInterface
{
    /**
     * @param array<string, mixed> $context
     */
    public function render(Block $templateBlock, array $context = []): string;
}

class_alias(BlockRendererInterface::class, '\Sylius\Bundle\UiBundle\Renderer\TemplateBlockRendererInterface');
