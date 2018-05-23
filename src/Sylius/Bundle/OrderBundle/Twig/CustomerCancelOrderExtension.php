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

namespace Sylius\Bundle\OrderBundle\Twig;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\OrderShippingStates;
use Twig\TwigFilter;

final class CustomerCancelOrderExtension extends \Twig_Extension implements CustomerCancelOrderExtensionInterface
{
    public function getFilters(): array
    {
        return [new TwigFilter('can_customer_cancel_order', [$this, 'canOrderBeCancelled'])];
    }

    public function canOrderBeCancelled(OrderInterface $order)
    {
        return $order->getShippingState() === OrderShippingStates::STATE_CART
            && $order->getPaymentState() === OrderPaymentStates::STATE_CART;
    }
}
