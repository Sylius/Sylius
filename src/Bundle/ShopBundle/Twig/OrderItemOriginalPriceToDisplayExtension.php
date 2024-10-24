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
        if (
            $item->getOriginalUnitPrice() !== null &&
            ($item->getOriginalUnitPrice() > $item->getUnitPrice() || $item->getOriginalUnitPrice() > $item->getDiscountedUnitPrice())
        ) {
            return $item->getOriginalUnitPrice();
        }

        if ($item->getOriginalUnitPrice() === null && $item->getUnitPrice() > $item->getDiscountedUnitPrice()) {
            return $item->getUnitPrice();
        }

        return null;
    }
}
