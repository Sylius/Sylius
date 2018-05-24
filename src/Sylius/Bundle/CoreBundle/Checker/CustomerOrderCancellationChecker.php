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

namespace Sylius\Bundle\CoreBundle\Checker;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\OrderShippingStates;

final class CustomerOrderCancellationChecker implements CustomerOrderCancellationCheckerInterface
{
    public function check(OrderInterface $order): bool
    {
        $shippingState = $order->getShippingState();
        $paymentState = $order->getPaymentState();

        $isNotShipped = $shippingState === OrderShippingStates::STATE_CART || $shippingState === OrderShippingStates::STATE_READY;
        $isNotPaid = $paymentState === OrderPaymentStates::STATE_CART || $paymentState === OrderPaymentStates::STATE_AWAITING_PAYMENT;

        return $isNotShipped && $isNotPaid;
    }
}
