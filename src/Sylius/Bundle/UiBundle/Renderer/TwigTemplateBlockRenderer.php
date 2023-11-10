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

use Sylius\Bundle\UiBundle\ContextProvider\ContextProviderInterface;
use Sylius\Bundle\UiBundle\Registry\Block;
use Sylius\Bundle\UiBundle\Registry\TemplateBlock;
use Twig\Environment;

final class TwigTemplateBlockRenderer implements SupportableBlockRendererInterface
{
    /**
     * @param iterable<ContextProviderInterface> $contextProviders
     */
    public function __construct(private Environment $twig, private iterable $contextProviders)
    {
    }

    /**
     * @param TemplateBlock $templateBlock
     */
    public function render(Block $templateBlock, array $context = []): string
    {
        if (!$this->supports($templateBlock)) {
            throw new \InvalidArgumentException(sprintf(
                'Block "%s" is not supported by "%s".',
                $templateBlock->getName(),
                self::class,
            ));
        }

        foreach ($this->contextProviders as $contextProvider) {
            if (!$contextProvider instanceof ContextProviderInterface || !$contextProvider->supports($templateBlock)) {
                continue;
            }

            $context = $contextProvider->provide($context, $templateBlock);
        }

        return $this->twig->render($templateBlock->getTemplate(), $context);
    }

    public function supports(Block $block): bool
    {
        return is_a($block, TemplateBlock::class);
    }
}
