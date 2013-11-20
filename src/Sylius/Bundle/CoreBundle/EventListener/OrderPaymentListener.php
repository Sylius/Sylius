<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EventListener;

use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\CoreBundle\OrderProcessing\PaymentProcessorInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Order payment listener.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderPaymentListener
{
    /**
     * Order payment processor.
     *
     * @var PaymentProcessorInterface
     */
    protected $paymentProcessor;

    /**
     * Constructor.
     *
     * @param PaymentProcessorInterface $paymentProcessor
     */
    public function __construct(PaymentProcessorInterface $paymentProcessor)
    {
        $this->paymentProcessor = $paymentProcessor;
    }

    /**
     * Get the order from event and create payment.
     *
     * @param GenericEvent $event
     */
    public function processOrderPayment(GenericEvent $event)
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            throw new \InvalidArgumentException(
                'Order payment listener requires event subject to be instance of "Sylius\Bundle\CoreBundle\Model\OrderInterface"'
            );
        }

        $this->paymentProcessor->createPayment($order);
    }
}
