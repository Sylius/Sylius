<?php

declare(strict_types=1);

namespace Sylius\Bundle\UiBundle\Renderer;

use Sylius\Bundle\UiBundle\Registry\TemplateBlock;
use Sylius\Bundle\UiBundle\Registry\TemplateBlockRegistryInterface;

final class HtmlDebugTemplateEventRenderer implements TemplateEventRendererInterface
{
    /**
     * @var TemplateEventRendererInterface
     */
    private $templateEventRenderer;

    /**
     * @var TemplateBlockRegistryInterface
     */
    private $templateBlockRegistry;

    public function __construct(TemplateEventRendererInterface $templateEventRenderer, TemplateBlockRegistryInterface $templateBlockRegistry)
    {
        $this->templateEventRenderer = $templateEventRenderer;
        $this->templateBlockRegistry = $templateBlockRegistry;
    }

    public function render(string $eventName, array $context = []): string
    {
        $shouldRenderHtmlDebug = $this->hasAtLeastOneHtmlTemplate($this->templateBlockRegistry->findEnabledForEvent($eventName));

        $renderedEvent = '';

        if ($shouldRenderHtmlDebug) {
            $renderedEvent .= sprintf(
                '<!-- BEGIN EVENT | event name: "%s" -->',
                $eventName
            );
            $renderedEvent .= "\n";
        }

        $renderedEvent .= $this->templateEventRenderer->render($eventName, $context);

        if ($shouldRenderHtmlDebug) {
            $renderedEvent .= "\n";
            $renderedEvent .= sprintf(
                '<!-- END EVENT | event name: "%s" -->',
                $eventName
            );
        }

        return $renderedEvent;
    }

    /**
     * @param TemplateBlock[] $templateBlocks
     */
    private function hasAtLeastOneHtmlTemplate(array $templateBlocks): bool
    {
        return count(array_filter($templateBlocks, static function (TemplateBlock $templateBlock): bool {
            return strrpos($templateBlock->getTemplate(), '.html.twig') !== false;
        })) >= 1;
    }
}
