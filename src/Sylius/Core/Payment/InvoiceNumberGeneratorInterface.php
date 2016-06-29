<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Core\Payment;

use Sylius\Order\Model\OrderInterface;
use Sylius\Payment\Model\PaymentInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface InvoiceNumberGeneratorInterface
{
    /**
     * @param OrderInterface $order
     * @param PaymentInterface $payment
     *
     * @return string
     */
    public function generate(OrderInterface $order, PaymentInterface $payment);
}
