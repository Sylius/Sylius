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
use Sylius\Bundle\UiBundle\Registry\BlockRegistryInterface;
use Sylius\Bundle\UiBundle\Registry\ComponentBlock;
use Sylius\Bundle\UiBundle\Registry\TemplateBlock;

final class HtmlDebugTwigEventRenderer implements TwigEventRendererInterface
{
    public function __construct(
        private TwigEventRendererInterface $templateEventRenderer,
        private BlockRegistryInterface $blockRegistry,
    ) {
    }

    /**
     * @param non-empty-list<string> $eventNames
     * @param array<string, mixed> $context
     */
    public function render(array $eventNames, array $context = []): string
    {
        $shouldRenderHtmlDebug = $this->shouldRenderHtmlDebug($this->blockRegistry->findEnabledForEvents($eventNames));

        $renderedParts = [];

        if ($shouldRenderHtmlDebug) {
            $renderedParts[] = sprintf(
                '<!-- BEGIN EVENT | event name: "%s" -->',
                implode(', ', $eventNames),
            );
        }

        $renderedParts[] = $this->templateEventRenderer->render($eventNames, $context);

        if ($shouldRenderHtmlDebug) {
            $renderedParts[] = sprintf(
                '<!-- END EVENT | event name: "%s" -->',
                implode(', ', $eventNames),
            );
        }

        return implode("\n", $renderedParts);
    }

    /**
     * @param Block[] $blocks
     */
    private function shouldRenderHtmlDebug(array $blocks): bool
    {
        if (0 === count($blocks)) {
            return true;
        }

        return 0 !== count(
            array_filter($blocks, function (Block $block): bool {
                return match (get_class($block)) {
                    TemplateBlock::class => strrpos($block->getTemplate(), '.html.twig') !== false,
                    ComponentBlock::class => true,
                    default => false,
                };
            }),
        );
    }
}

class_alias(HtmlDebugTwigEventRenderer::class, '\Sylius\Bundle\UiBundle\Renderer\HtmlDebugTemplateEventRenderer');
