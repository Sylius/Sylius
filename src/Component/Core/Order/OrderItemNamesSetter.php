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

namespace Sylius\Component\Core\Order;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;

final class OrderItemNamesSetter implements OrderItemNamesSetterInterface
{
    public function __invoke(OrderInterface $order): void
    {
        $localeCode = $order->getLocaleCode();

        /** @var OrderItemInterface $item */
        foreach ($order->getItems() as $item) {
            $variant = $item->getVariant();

            if (null !== $variant) {
                $item->setVariantName($variant->getTranslation($localeCode)->getName());
            }

            if (null !== $variant && null !== $variant->getProduct()) {
                $item->setProductName($variant->getProduct()->getTranslation($localeCode)->getName());
            }
        }
    }
}
