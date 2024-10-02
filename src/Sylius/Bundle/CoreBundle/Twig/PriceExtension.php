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

namespace Sylius\Bundle\CoreBundle\Twig;

use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Webmozart\Assert\Assert;

final class PriceExtension extends AbstractExtension
{
    public function __construct(private readonly ProductVariantPricesCalculatorInterface $productVariantPricesCalculator)
    {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('sylius_calculate_price', [$this, 'getPrice']),
            new TwigFilter('sylius_calculate_original_price', [$this, 'getOriginalPrice']),
            new TwigFilter('sylius_has_discount', [$this, 'hasDiscount']),
            new TwigFilter('sylius_has_lowest_price', [$this, 'hasLowestPriceBeforeDiscount']),
            new TwigFilter('sylius_calculate_lowest_price', [$this, 'getLowestPriceBeforeDiscount']),
        ];
    }

    /**
     * @param array<array-key, mixed> $context
     *
     * @throws \InvalidArgumentException
     */
    public function getPrice(ProductVariantInterface $productVariant, array $context): int
    {
        Assert::keyExists($context, 'channel');

        return $this->productVariantPricesCalculator->calculate($productVariant, $context);
    }

    /**
     * @param array<array-key, mixed> $context
     *
     * @throws \InvalidArgumentException
     */
    public function getOriginalPrice(ProductVariantInterface $productVariant, array $context): int
    {
        Assert::keyExists($context, 'channel');

        return $this->productVariantPricesCalculator->calculateOriginal($productVariant, $context);
    }

    /**
     * @param array<array-key, mixed> $context
     *
     * @throws \InvalidArgumentException
     */
    public function getLowestPriceBeforeDiscount(ProductVariantInterface $productVariant, array $context): ?int
    {
        Assert::keyExists($context, 'channel');

        return $this->productVariantPricesCalculator->calculateLowestPriceBeforeDiscount($productVariant, $context);
    }

    /**
     * @param array<array-key, mixed> $context
     *
     * @throws \InvalidArgumentException
     */
    public function hasLowestPriceBeforeDiscount(ProductVariantInterface $productVariant, array $context): bool
    {
        Assert::keyExists($context, 'channel');

        return null !== $this->getLowestPriceBeforeDiscount($productVariant, $context);
    }

    /**
     * @param array<array-key, mixed> $context
     *
     * @throws \InvalidArgumentException
     */
    public function hasDiscount(ProductVariantInterface $productVariant, array $context): bool
    {
        Assert::keyExists($context, 'channel');

        return $this->getOriginalPrice($productVariant, $context) > $this->getPrice($productVariant, $context);
    }
}
