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

namespace Sylius\Bundle\UiBundle\DataCollector;

use Sylius\Bundle\UiBundle\Registry\TemplateBlock;
use Sylius\Bundle\UiBundle\Renderer\TemplateBlockRendererInterface;

/** @internal */
final class TraceableTemplateBlockRenderer implements TemplateBlockRendererInterface
{
    public function __construct(
        private TemplateBlockRendererInterface $templateBlockRenderer,
        private TemplateBlockRenderingHistory $templateBlockRenderingHistory,
    ) {
    }

    public function render(TemplateBlock $templateBlock, array $context = []): string
    {
        $this->templateBlockRenderingHistory->startRenderingBlock($templateBlock, $context);

        $renderedBlock = $this->templateBlockRenderer->render($templateBlock, $context);

        $this->templateBlockRenderingHistory->stopRenderingBlock($templateBlock, $context);

        return $renderedBlock;
    }
}
