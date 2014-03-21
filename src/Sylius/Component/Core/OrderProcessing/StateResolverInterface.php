<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\OrderProcessing;

use Sylius\Component\Core\Model\OrderInterface;

/**
 * Order state resolver.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface StateResolverInterface
{
    /**
     * Figure out the order payment state.
     *
     * @param OrderInterface $order
     */
    public function resolvePaymentState(OrderInterface $order);

    /**
     * Set correct shipping state on the order.
     *
     * @param OrderInterface $order
     */
    public function resolveShippingState(OrderInterface $order);
}
