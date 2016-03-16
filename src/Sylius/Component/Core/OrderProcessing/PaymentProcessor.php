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
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\Factory\PaymentFactoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class PaymentProcessor implements PaymentProcessorInterface
{
    /**
     * @var PaymentFactoryInterface
     */
    protected $paymentFactory;

    /**
     * @param PaymentFactoryInterface $paymentFactory
     */
    public function __construct(PaymentFactoryInterface $paymentFactory)
    {
        $this->paymentFactory = $paymentFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function processOrderPayments(OrderInterface $order)
    {
        /** @var $payment PaymentInterface */
        $payment = $this->paymentFactory->createWithAmountAndCurrency($order->getTotal(), $order->getCurrency());

        $order->addPayment($payment);
    }
}
