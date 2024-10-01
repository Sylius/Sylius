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

use Sylius\Bundle\CoreBundle\Templating\Helper\PriceHelper;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Webmozart\Assert\Assert;

final class PriceExtension extends AbstractExtension
{
    public function __construct(private readonly PriceHelper|ProductVariantPricesCalculatorInterface $helper)
    {
        if ($this->helper instanceof PriceHelper) {
            trigger_deprecation(
                'sylius/core-bundle',
                '1.14',
                'Passing an instance of %s as constructor argument for %s is deprecated and will be prohibited in Sylius 2.0. Pass an instance of %s instead.',
                PriceHelper::class,
                self::class,
                ProductVariantPricesCalculatorInterface::class,
            );

            trigger_deprecation(
                'sylius/core-bundle',
                '1.14',
                'The argument name $helper is deprecated and will be renamed to $productVariantPriceCalculator in Sylius 2.0.',
            );
        }
    }

    public function getFilters(): array
    {
        if ($this->helper instanceof ProductVariantPricesCalculatorInterface) {
            return [
                new TwigFilter('sylius_calculate_price', [$this, 'getPrice']),
                new TwigFilter('sylius_calculate_original_price', [$this, 'getOriginalPrice']),
                new TwigFilter('sylius_has_discount', [$this, 'hasDiscount']),
                new TwigFilter('sylius_has_lowest_price', [$this, 'hasLowestPriceBeforeDiscount']),
                new TwigFilter('sylius_calculate_lowest_price', [$this, 'getLowestPriceBeforeDiscount']),
            ];
        }

        return [
            new TwigFilter('sylius_calculate_price', [$this->helper, 'getPrice']),
            new TwigFilter('sylius_calculate_original_price', [$this->helper, 'getOriginalPrice']),
            new TwigFilter('sylius_has_discount', [$this->helper, 'hasDiscount']),
            new TwigFilter('sylius_has_lowest_price', [$this->helper, 'hasLowestPriceBeforeDiscount']),
            new TwigFilter('sylius_calculate_lowest_price', [$this->helper, 'getLowestPriceBeforeDiscount']),
        ];
    }

    /**
     * @param array<array-key, mixed> $context
     *
     * @throws \InvalidArgumentException
     */
    private function getPrice(ProductVariantInterface $productVariant, array $context): int
    {
        Assert::keyExists($context, 'channel');

        return $this->helper->calculate($productVariant, $context);
    }

    /**
     * @param array<array-key, mixed> $context
     *
     * @throws \InvalidArgumentException
     */
    private function getOriginalPrice(ProductVariantInterface $productVariant, array $context): int
    {
        Assert::keyExists($context, 'channel');

        return $this->helper->calculateOriginal($productVariant, $context);
    }

    /**
     * @param array<array-key, mixed> $context
     *
     * @throws \InvalidArgumentException
     */
    private function getLowestPriceBeforeDiscount(ProductVariantInterface $productVariant, array $context): ?int
    {
        Assert::keyExists($context, 'channel');

        if (\method_exists($this->helper, 'calculateLowestPriceBeforeDiscount')) {
            return $this->helper->calculateLowestPriceBeforeDiscount($productVariant, $context);
        }

        return $this->getPrice($productVariant, $context);
    }

    /**
     * @param array<array-key, mixed> $context
     *
     * @throws \InvalidArgumentException
     */
    private function hasLowestPriceBeforeDiscount(ProductVariantInterface $productVariant, array $context): bool
    {
        Assert::keyExists($context, 'channel');

        return null !== $this->getLowestPriceBeforeDiscount($productVariant, $context);
    }

    /**
     * @param array<array-key, mixed> $context
     *
     * @throws \InvalidArgumentException
     */
    private function hasDiscount(ProductVariantInterface $productVariant, array $context): bool
    {
        Assert::keyExists($context, 'channel');

        return $this->getOriginalPrice($productVariant, $context) > $this->getPrice($productVariant, $context);
    }
}
