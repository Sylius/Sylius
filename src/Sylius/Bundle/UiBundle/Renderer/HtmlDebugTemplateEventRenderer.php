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
use Sylius\Bundle\UiBundle\Registry\TemplateBlockRegistryInterface;

/**
 * @experimental
 */
final class HtmlDebugTemplateEventRenderer implements TemplateEventRendererInterface
{
    /** @var TemplateEventRendererInterface */
    private $templateEventRenderer;

    /** @var TemplateBlockRegistryInterface */
    private $templateBlockRegistry;

    public function __construct(TemplateEventRendererInterface $templateEventRenderer, TemplateBlockRegistryInterface $templateBlockRegistry)
    {
        $this->templateEventRenderer = $templateEventRenderer;
        $this->templateBlockRegistry = $templateBlockRegistry;
    }

    public function render(array $eventNames, array $context = []): string
    {
        $shouldRenderHtmlDebug = $this->shouldRenderHtmlDebug($this->templateBlockRegistry->findEnabledForEvents($eventNames));

        $renderedParts = [];

        if ($shouldRenderHtmlDebug) {
            $renderedParts[] = sprintf(
                '<!-- BEGIN EVENT | event name: "%s" -->',
                implode(', ', $eventNames)
            );
        }

        $renderedParts[] = $this->templateEventRenderer->render($eventNames, $context);

        if ($shouldRenderHtmlDebug) {
            $renderedParts[] = sprintf(
                '<!-- END EVENT | event name: "%s" -->',
                implode(', ', $eventNames)
            );
        }

        return implode("\n", $renderedParts);
    }

    /**
     * @param TemplateBlock[] $templateBlocks
     */
    private function shouldRenderHtmlDebug(array $templateBlocks): bool
    {
        return count($templateBlocks) === 0 || count(array_filter($templateBlocks, static function (TemplateBlock $templateBlock): bool {
            return strrpos($templateBlock->getTemplate(), '.html.twig') !== false;
        })) >= 1;
    }
}
