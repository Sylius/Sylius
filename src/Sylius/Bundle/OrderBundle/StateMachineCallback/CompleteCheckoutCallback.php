<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\StateMachineCallback;

use Sylius\Component\Order\Model\OrderInterface;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class CompleteCheckoutCallback
{
    /**
     * @param OrderInterface $order
     */
    public function completeCheckout(OrderInterface $order)
    {
        $order->completeCheckout();
    }
}
