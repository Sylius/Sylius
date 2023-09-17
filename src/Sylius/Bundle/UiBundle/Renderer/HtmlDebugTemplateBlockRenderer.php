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
        $isTemplateBlockComponent = null !== $templateBlock->getComponent();
        $shouldRenderHtmlDebug = $isTemplateBlockComponent || strrpos($templateBlock->getTemplate(), '.html.twig') !== false;

        if (!$shouldRenderHtmlDebug) {
            return $this->templateBlockRenderer->render($templateBlock, $context);
        }

        $renderedParts = [];

        if ($isTemplateBlockComponent) {
            $renderedParts[] = $this->getBeginBlockForComponent($templateBlock);
        } else {
            $renderedParts[] = $this->getBeginBlockForTemplate($templateBlock);
        }

        $renderedParts[] = $this->templateBlockRenderer->render($templateBlock, $context);
        $renderedParts[] = $this->getEndBlock($templateBlock);

        return implode("\n", $renderedParts);
    }

    private function getBeginBlockForTemplate(TemplateBlock $templateBlock): string
    {
        return sprintf(
            '<!-- BEGIN BLOCK | event name: "%s", block name: "%s", template: "%s", priority: %d -->',
            $templateBlock->getEventName(),
            $templateBlock->getName(),
            $templateBlock->getTemplate(),
            $templateBlock->getPriority(),
        );
    }

    private function getBeginBlockForComponent(TemplateBlock $templateBlock): string
    {
        return sprintf(
            '<!-- BEGIN BLOCK | event name: "%s", block name: "%s", component: "%s", priority: %d -->',
            $templateBlock->getEventName(),
            $templateBlock->getName(),
            $templateBlock->getComponent(),
            $templateBlock->getPriority(),
        );
    }

    private function getEndBlock(TemplateBlock $templateBlock): string
    {
        return sprintf(
            '<!-- END BLOCK | event name: "%s", block name: "%s" -->',
            $templateBlock->getEventName(),
            $templateBlock->getName(),
        );
    }
}
