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
use Symfony\UX\TwigComponent\ComponentRendererInterface;

/** @internal */
final class TwigComponentBlockRenderer implements TemplateBlockRendererInterface
{
    public function __construct(
        private TemplateBlockRendererInterface $decoratedRenderer,
        private ComponentRendererInterface $componentRenderer,
    ) {
    }

    /**
     * @param array<string, mixed> $context
     */
    public function render(TemplateBlock $templateBlock, array $context = []): string
    {
        if (null === $templateBlock->getComponent()) {
            return $this->decoratedRenderer->render($templateBlock, $context);
        }

        return $this->componentRenderer->createAndRender($templateBlock->getComponent(), ['context' => $templateBlock->getContext() + $context]);
    }
}
