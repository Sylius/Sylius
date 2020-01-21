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

use Sylius\Bundle\UiBundle\Registry\TemplateBlock;

final class HtmlDebugTemplateBlockRenderer implements TemplateBlockRendererInterface
{
    /** @var TemplateBlockRendererInterface */
    private $templateBlockRenderer;

    public function __construct(TemplateBlockRendererInterface $templateBlockRenderer)
    {
        $this->templateBlockRenderer = $templateBlockRenderer;
    }

    public function render(string $eventName, TemplateBlock $templateBlock, array $context = []): string
    {
        $shouldRenderHtmlDebug = strrpos($templateBlock->getTemplate(), '.html.twig') !== false;

        $renderedParts = [];

        if ($shouldRenderHtmlDebug) {
            $renderedParts[] = sprintf(
                '<!-- BEGIN BLOCK | event name: "%s", block name: "%s", template: "%s", priority: %d -->',
                $eventName,
                $templateBlock->getName(),
                $templateBlock->getTemplate(),
                $templateBlock->getPriority()
            );
        }

        $renderedParts[] = $this->templateBlockRenderer->render($eventName, $templateBlock, $context);

        if ($shouldRenderHtmlDebug) {
            $renderedParts[] = sprintf(
                '<!-- END BLOCK | event name: "%s", block name: "%s" -->',
                $eventName,
                $templateBlock->getName()
            );
        }

        return implode("\n", $renderedParts);
    }
}
