<?php

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
        $renderedBlock = '';
        if (strrpos($templateBlock->getTemplate(), '.html.twig') !== false) {
            $renderedBlock .= sprintf(
                '<!-- event name: "%s", block name: "%s", template: "%s", priority: %d -->',
                $eventName,
                $templateBlock->getName(),
                $templateBlock->getTemplate(),
                $templateBlock->getPriority()
            );
            $renderedBlock .= "\n";
        }

        $renderedBlock .= $this->templateBlockRenderer->render($eventName, $templateBlock, $context);

        return $renderedBlock;
    }
}
