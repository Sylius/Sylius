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

namespace Sylius\Bundle\CoreBundle\Applicator;

use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class CatalogPromotionApplicator implements CatalogPromotionApplicatorInterface
{
    public function applyPercentageDiscount(ProductVariantInterface $variant, float $discount): void
    {
        /** @var ChannelPricingInterface $channelPricing */
        foreach ($variant->getChannelPricings() as $channelPricing) {
            if ($channelPricing->getOriginalPrice() === null) {
                $channelPricing->setOriginalPrice($channelPricing->getPrice());
            }
            $channelPricing->setPrice((int) ($channelPricing->getPrice() - ($channelPricing->getPrice() * $discount)));
        }
    }
}
