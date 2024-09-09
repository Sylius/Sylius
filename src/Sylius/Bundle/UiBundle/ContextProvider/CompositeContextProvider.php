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

namespace Sylius\Bundle\UiBundle\ContextProvider;

use Sylius\Bundle\UiBundle\ContextProvider\Exception\NoSupportedContextProvider;
use Sylius\Bundle\UiBundle\Registry\Block;
use Webmozart\Assert\Assert;

final class CompositeContextProvider implements ContextProviderInterface
{
    /** @var array<ContextProviderInterface> */
    private array $contextProviders;

    /**
     * @param iterable<ContextProviderInterface> $contextProviders
     */
    public function __construct(iterable $contextProviders)
    {
        Assert::allIsInstanceOf($contextProviders, ContextProviderInterface::class);
        $this->contextProviders = $contextProviders instanceof \Traversable ? iterator_to_array($contextProviders) : $contextProviders;
    }

    /**
     * @param array<string, mixed> $templateContext
     *
     * @return array<string, mixed>
     */
    public function provide(array $templateContext, Block $templateBlock): array
    {
        foreach ($this->contextProviders as $contextProvider) {
            if (!$contextProvider->supports($templateBlock)) {
                continue;
            }

            return $contextProvider->provide($templateContext, $templateBlock);
        }

        throw new NoSupportedContextProvider(sprintf('No supported context provider found for block "%s".', $templateBlock->getName()));
    }

    public function supports(Block $templateBlock): bool
    {
        return true;
    }
}
