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

use Sylius\Bundle\UiBundle\Registry\TemplateBlock;

final class HtmlDebugTemplateBlockRenderer implements TemplateBlockRendererInterface
{
    public function __construct(private TemplateBlockRendererInterface $templateBlockRenderer)
    {
    }

    public function render(TemplateBlock $templateBlock, array $context = []): string
    {
        $shouldRenderHtmlDebug = strrpos($templateBlock->getTemplate(), '.html.twig') !== false;

        $renderedParts = [];

        if ($shouldRenderHtmlDebug) {
            $renderedParts[] = sprintf(
                '<!-- BEGIN BLOCK | event name: "%s", block name: "%s", template: "%s", priority: %d -->',
                $templateBlock->getEventName(),
                $templateBlock->getName(),
                $templateBlock->getTemplate(),
                $templateBlock->getPriority(),
            );
        }

        $renderedParts[] = $this->templateBlockRenderer->render($templateBlock, $context);

        if ($shouldRenderHtmlDebug) {
            $renderedParts[] = sprintf(
                '<!-- END BLOCK | event name: "%s", block name: "%s" -->',
                $templateBlock->getEventName(),
                $templateBlock->getName(),
            );
        }

        return implode("\n", $renderedParts);
    }
}
