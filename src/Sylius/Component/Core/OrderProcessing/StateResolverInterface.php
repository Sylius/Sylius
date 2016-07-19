<?php

/*
 * This file is a part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\OrderProcessing;

use Sylius\Component\Core\Model\OrderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface StateResolverInterface
{
    /**
     * @param OrderInterface $order
     */
    public function resolvePaymentState(OrderInterface $order);

    /**
     * @param OrderInterface $order
     */
    public function resolveShippingState(OrderInterface $order);
}
