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
 * Order shipping charges applicator.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ShippingChargesProcessorInterface
{
    /**
     * Apply shipping charges to order.
     *
     * @param OrderInterface $order
     */
    public function applyShippingCharges(OrderInterface $order);
}
