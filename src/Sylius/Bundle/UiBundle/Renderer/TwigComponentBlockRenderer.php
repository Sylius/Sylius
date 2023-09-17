<?php

namespace Sylius\Bundle\UiBundle\Renderer;

use Sylius\Bundle\UiBundle\Registry\TemplateBlock;
use Symfony\UX\TwigComponent\ComponentRendererInterface;

final class TwigComponentBlockRenderer implements TemplateBlockRendererInterface
{
    public function __construct (
        private TemplateBlockRendererInterface $decoratedRenderer,
        private ComponentRendererInterface $componentRenderer,
    ) {
    }

    public function render(TemplateBlock $templateBlock, array $context = []): string
    {
        if (null === $templateBlock->getComponent()) {
            return $this->decoratedRenderer->render($templateBlock, $context);
        }

        return $this->componentRenderer->createAndRender($templateBlock->getComponent(), $templateBlock->getContext());
    }
}
