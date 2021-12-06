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

namespace Sylius\Bundle\ShopBundle\Twig;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderItem;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class OrderItemOriginalPriceToDisplayExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('sylius_order_item_original_price_to_display', [$this, 'getOriginalPriceToDisplay']),
        ];
    }

    public function getOriginalPriceToDisplay(OrderItem $item, ChannelInterface $channel): ?int
    {
        $variant = $item->getVariant();

        $channelPricing = $variant->getChannelPricingForChannel($channel);
        $originalPrice = $channelPricing->getOriginalPrice();
        $price = $channelPricing->getPrice();

        $regularPrice = $item->getUnitPrice();
        $discountedPrice = $item->getDiscountedUnitPrice();

        if ($originalPrice !== null && $originalPrice > $price) {
            return $originalPrice;
        }

        if ($originalPrice === null && $regularPrice > $discountedPrice) {
            return $regularPrice;
        }

        return null;
    }
}
