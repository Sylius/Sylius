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

namespace Sylius\Bundle\CoreBundle\Templating\Helper;

use Sylius\Component\Core\Calculator\ProductVariantPriceCalculatorInterface;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\Templating\Helper\Helper;
use Webmozart\Assert\Assert;

class PriceHelper extends Helper
{
    public function __construct(private ProductVariantPriceCalculatorInterface $productVariantPriceCalculator)
    {
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function getPrice(ProductVariantInterface $productVariant, array $context): int
    {
        Assert::keyExists($context, 'channel');

        return $this
            ->productVariantPriceCalculator
            ->calculate($productVariant, $context)
        ;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function getOriginalPrice(ProductVariantInterface $productVariant, array $context): int
    {
        Assert::keyExists($context, 'channel');
        Assert::isInstanceOf($this->productVariantPriceCalculator, ProductVariantPricesCalculatorInterface::class);

        return $this
            ->productVariantPriceCalculator
            ->calculateOriginal($productVariant, $context)
        ;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function hasDiscount(ProductVariantInterface $productVariant, array $context): bool
    {
        Assert::keyExists($context, 'channel');
        Assert::isInstanceOf($this->productVariantPriceCalculator, ProductVariantPricesCalculatorInterface::class);

        return $this->getOriginalPrice($productVariant, $context) > $this->getPrice($productVariant, $context);
    }

    public function getName(): string
    {
        return 'sylius_calculate_price';
    }
}
