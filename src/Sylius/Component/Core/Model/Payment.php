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

use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Payment\Model\Payment as BasePayment;

/**
 * Payment attached to order.
 *
 * @author Ka Yue Yeung <kayuey@gmail.com>
 */
class Payment extends BasePayment implements PaymentInterface
{
    /**
     * Order instance.
     *
     * @var BaseOrderInterface
     */
    protected $order;

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * {@inheritdoc}
     */
    public function setOrder(BaseOrderInterface $order = null)
    {
        $this->order = $order;

        return $this;
    }
}
