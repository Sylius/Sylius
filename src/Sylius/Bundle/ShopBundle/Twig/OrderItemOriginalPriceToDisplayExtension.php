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

namespace Sylius\Bundle\ShopBundle\Twig;

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

    public function getOriginalPriceToDisplay(OrderItem $item): ?int
    {
        $unitPrice = $item->getUnitPrice();
        $originalUnitPrice = $item->getOriginalUnitPrice();
        $discountedUnitPrice = $item->getDiscountedUnitPrice();

        if (
            $originalUnitPrice !== null &&
            ($originalUnitPrice > $unitPrice || $originalUnitPrice > $discountedUnitPrice)
        ) {
            return $originalUnitPrice;
        }

        if ($originalUnitPrice === null && $unitPrice > $discountedUnitPrice) {
            return $unitPrice;
        }

        return null;
    }
}
