<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Provider\ProductVariantMap;

use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class ProductVariantLowestPriceMapProvider implements ProductVariantMapProviderInterface
{
    public function __construct(private ProductVariantPricesCalculatorInterface $calculator)
    {
    }

    public function provide(ProductVariantInterface $variant, ChannelInterface $channel): array
    {
        return [
            'lowest-price-before-discount' => $this->calculator->calculateLowestPriceBeforeDiscount($variant, ['channel' => $channel]),
        ];
    }

    public function supports(ProductVariantInterface $variant, ChannelInterface $channel): bool
    {
        return
            null !== $variant->getChannelPricingForChannel($channel) &&
            null !== $this->calculator->calculateLowestPriceBeforeDiscount($variant, ['channel' => $channel])
        ;
    }
}
