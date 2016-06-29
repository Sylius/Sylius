<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Core\Test\Services;

use Sylius\Core\Payment\InvoiceNumberGeneratorInterface;
use Sylius\Order\Model\OrderInterface;
use Sylius\Payment\Model\PaymentInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class RandomInvoiceNumberGenerator implements InvoiceNumberGeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function generate(OrderInterface $order, PaymentInterface $payment)
    {
        return mt_rand(1, 100000).'-'.mt_rand(1, 100000);
    }
}
