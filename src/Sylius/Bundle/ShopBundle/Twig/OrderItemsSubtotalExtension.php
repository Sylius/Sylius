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

use Sylius\Bundle\ShopBundle\Calculator\OrderItemsSubtotalCalculatorInterface;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\OrderInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

trigger_deprecation(
    'sylius/shop-bundle',
    '1.13',
    'The "%s" class is deprecated and will be removed in Sylius 2.0. Use method "getItemsSubtotal" from "%s" instead.',
    OrderItemsSubtotalExtension::class,
    Order::class,
);

/**
 * @deprecated since Sylius 1.13 and will be removed in Sylius 2.0. Items subtotal calculations is now available by using {@see Order::getSubtotalItems} method.
 */
class OrderItemsSubtotalExtension extends AbstractExtension
{
    public function __construct(private ?OrderItemsSubtotalCalculatorInterface $calculator = null)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sylius_order_items_subtotal', [$this, 'getSubtotal'], ['deprecated' => true]),
        ];
    }

    public function getSubtotal(OrderInterface $order): int
    {
        if (null === $this->calculator) {
            return $order->getItemsSubtotal();
        }

        return $this->calculator->getSubtotal($order);
    }
}
