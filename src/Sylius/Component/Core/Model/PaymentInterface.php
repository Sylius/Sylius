<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Model;

use Sylius\Component\Payment\Model\PaymentInterface as BasePaymentInterface;

/**
 * Payment interface.
 *
 * @author Ka Yue Yeung <kayuey@gmail.com>
 */
interface PaymentInterface extends BasePaymentInterface
{
    /**
     * Get the order.
     *
     * @return OrderInterface
     */
    public function getOrder();

    /**
     * Set the order.
     *
     * @param OrderInterface $order
     */
    public function setOrder(OrderInterface $order = null);
}
