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
 * Order payment processor interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface PaymentProcessorInterface
{
    /**
     * @param OrderInterface $order
     */
    public function processOrderPayments(OrderInterface $order);

    /**
     * @param OrderInterface $order
     */
    public function createNewPaymentForOrder(OrderInterface $order);
}
