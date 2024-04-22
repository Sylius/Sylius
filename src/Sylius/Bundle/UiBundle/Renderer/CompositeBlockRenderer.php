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
use Sylius\Bundle\UiBundle\Renderer\Exception\NoSupportedBlockRenderer;
use Webmozart\Assert\Assert;

/** @internal */
final class CompositeBlockRenderer implements BlockRendererInterface
{
    /** @var array<SupportableBlockRendererInterface> */
    private array $blockRenderers;

    /**
     * @param iterable<SupportableBlockRendererInterface> $blockRenderers
     */
    public function __construct(iterable $blockRenderers)
    {
        Assert::allIsInstanceOf($blockRenderers, SupportableBlockRendererInterface::class);
        $this->blockRenderers = $blockRenderers instanceof \Traversable ? iterator_to_array($blockRenderers) : $blockRenderers;
    }

    public function render(Block $templateBlock, array $context = []): string
    {
        foreach ($this->blockRenderers as $blockRenderer) {
            if (!$blockRenderer->supports($templateBlock)) {
                continue;
            }

            return $blockRenderer->render($templateBlock, $context);
        }

        throw new NoSupportedBlockRenderer(sprintf('No supported block renderer found for "%s" block.', $templateBlock->getName()));
    }
}
