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

namespace Sylius\Component\Core\Provider\ProductVariantMap;

use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Webmozart\Assert\Assert;

final class ProductVariantLowestPriceMapProvider implements ProductVariantMapProviderInterface
{
    public function __construct(private ProductVariantPricesCalculatorInterface $calculator)
    {
    }

    public function provide(ProductVariantInterface $variant, array $context): array
    {
        Assert::methodExists($this->calculator, 'calculateLowestPriceBeforeDiscount');

        return [
            'lowest-price-before-discount' => $this->calculator->calculateLowestPriceBeforeDiscount($variant, $context),
        ];
    }

    public function supports(ProductVariantInterface $variant, array $context): bool
    {
        if (!\method_exists($this->calculator, 'calculateLowestPriceBeforeDiscount')) {
            trigger_deprecation(
                'sylius/sylius',
                '1.13',
                'Not having `calculateLowestPriceBeforeDiscount` method on %s is deprecated since Sylius 1.13 and will be required in Sylius 2.0.',
                $this->calculator::class,
            );

            return false;
        }

        return
            isset($context['channel']) &&
            $context['channel'] instanceof ChannelInterface &&
            null !== $variant->getChannelPricingForChannel($context['channel']) &&
            null !== $this->calculator->calculateLowestPriceBeforeDiscount($variant, $context)
        ;
    }
}
