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
use Sylius\Bundle\UiBundle\Registry\ComponentBlock;
use Sylius\Bundle\UiBundle\Registry\TemplateBlock;

final class HtmlDebugBlockRenderer implements BlockRendererInterface
{
    public function __construct(private BlockRendererInterface $blockRenderer)
    {
    }

    public function render(Block $templateBlock, array $context = []): string
    {
        if (!$this->shouldRenderHtmlDebug($templateBlock)) {
            return $this->blockRenderer->render($templateBlock, $context);
        }

        $renderedParts = [];

        switch (get_class($templateBlock)) {
            case TemplateBlock::class:
                $renderedParts[] = $this->getBeginBlockForTemplate($templateBlock);

                break;
            case ComponentBlock::class:
                $renderedParts[] = $this->getBeginBlockForComponent($templateBlock);

                break;
        }

        $renderedParts[] = $this->blockRenderer->render($templateBlock, $context);
        $renderedParts[] = $this->getEndBlock($templateBlock);

        return implode("\n", $renderedParts);
    }

    private function shouldRenderHtmlDebug(Block $block): bool
    {
        return match (get_class($block)) {
            TemplateBlock::class => strrpos($block->getTemplate(), '.html.twig') !== false,
            ComponentBlock::class => true,
            default => false,
        };
    }

    private function getBeginBlockForTemplate(Block $block): string
    {
        if (!$block instanceof TemplateBlock) {
            throw new \InvalidArgumentException(sprintf('Block must be instance of "%s"', TemplateBlock::class));
        }

        return sprintf(
            '<!-- BEGIN BLOCK | event name: "%s", block name: "%s", template: "%s", priority: %d -->',
            $block->getEventName(),
            $block->getName(),
            $block->getTemplate(),
            $block->getPriority(),
        );
    }

    private function getBeginBlockForComponent(Block $block): string
    {
        if (!$block instanceof ComponentBlock) {
            throw new \InvalidArgumentException(sprintf('Block must be instance of "%s"', ComponentBlock::class));
        }

        return sprintf(
            '<!-- BEGIN BLOCK | event name: "%s", block name: "%s", component: "%s", priority: %d -->',
            $block->getEventName(),
            $block->getName(),
            $block->getComponentName(),
            $block->getPriority(),
        );
    }

    private function getEndBlock(Block $templateBlock): string
    {
        return sprintf(
            '<!-- END BLOCK | event name: "%s", block name: "%s" -->',
            $templateBlock->getEventName(),
            $templateBlock->getName(),
        );
    }
}

class_alias(HtmlDebugBlockRenderer::class, '\Sylius\Bundle\UiBundle\Renderer\HtmlDebugTemplateBlockRenderer');
