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
use Sylius\Component\Resource\Manager\DomainManagerInterface;

/**
 * Payment processor.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class PaymentProcessor implements PaymentProcessorInterface
{
    /**
     * Payment manager.
     *
     * @var DomainManagerInterface
     */
    protected $manager;

    /**
     * Constructor.
     *
     * @param DomainManagerInterface $manager
     */
    public function __construct(DomainManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function createPayment(OrderInterface $order)
    {
        /** @var $payment PaymentInterface */
        $payment = $this->manager->createNew();
        $payment->setCurrency($order->getCurrency());
        $payment->setAmount($order->getTotal());

        $order->addPayment($payment);

        return $payment;
    }
}
