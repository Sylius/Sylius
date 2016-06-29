<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Core\OrderProcessing;

use Sylius\Core\Model\OrderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ShippingChargesProcessorInterface
{
    /**
     * @param OrderInterface $order
     */
    public function applyShippingCharges(OrderInterface $order);
}
