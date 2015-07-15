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
 * Set an Order as completed
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class CompleteOrderCallback
{
    /**
     * Set an Order as completed
     *
     * @param OrderInterface $order
     */
    public function completeOrder(OrderInterface $order)
    {
        $order->complete();
    }
}
