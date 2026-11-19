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

namespace Sylius\Component\Core\Calculator;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Resolver\TaxRateResolverInterface;

final class TaxInclusivePricesCalculator implements ProductVariantPricesCalculatorInterface
{
    public function __construct(
        private readonly ProductVariantPricesCalculatorInterface $inner,
        private readonly TaxRateResolverInterface $taxRateResolver,
        private readonly CalculatorInterface $taxCalculator,
    ) {
    }

    public function calculate(ProductVariantInterface $productVariant, array $context): int
    {
        return $this->applyTax(
            $this->inner->calculate($productVariant, $context),
            $productVariant,
            $context,
        );
    }

    public function calculateOriginal(ProductVariantInterface $productVariant, array $context): int
    {
        return $this->applyTax(
            $this->inner->calculateOriginal($productVariant, $context),
            $productVariant,
            $context,
        );
    }

    public function calculateLowestPriceBeforeDiscount(ProductVariantInterface $productVariant, array $context): ?int
    {
        $price = $this->inner->calculateLowestPriceBeforeDiscount($productVariant, $context);
        if (null === $price) {
            return null;
        }

        return $this->applyTax($price, $productVariant, $context);
    }

    /** @param array<string, mixed> $context */
    private function applyTax(int $price, ProductVariantInterface $productVariant, array $context): int
    {
        /** @var ChannelInterface $channel */
        $channel = $context['channel'];

        if (!$channel->isShowPricesIncludingTax()) {
            return $price;
        }

        $zone = $channel->getDefaultTaxZone();
        if (null === $zone) {
            return $price;
        }

        $taxRate = $this->taxRateResolver->resolve($productVariant, ['zone' => $zone]);
        if (null === $taxRate || $taxRate->isIncludedInPrice()) {
            return $price;
        }

        return $price + (int) round($this->taxCalculator->calculate((float) $price, $taxRate));
    }
}
