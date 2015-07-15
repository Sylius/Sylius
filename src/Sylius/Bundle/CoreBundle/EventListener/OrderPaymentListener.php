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

use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderProcessing\PaymentChargesProcessorInterface;
use Sylius\Component\Core\OrderProcessing\PaymentProcessorInterface;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Order payment listener.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
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
     * @var PaymentChargesProcessorInterface
     */
    protected $paymentChargesProcessor;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * Constructor.
     *
     * @param PaymentProcessorInterface        $paymentProcessor
     * @param PaymentChargesProcessorInterface $paymentChargesProcessor
     * @param EventDispatcherInterface         $dispatcher
     * @param FactoryInterface                 $factory
     */
    public function __construct(
        PaymentProcessorInterface $paymentProcessor,
        PaymentChargesProcessorInterface $paymentChargesProcessor,
        EventDispatcherInterface $dispatcher,
        FactoryInterface $factory
    ) {
        $this->paymentProcessor        = $paymentProcessor;
        $this->paymentChargesProcessor = $paymentChargesProcessor;
        $this->dispatcher              = $dispatcher;
        $this->factory                 = $factory;
    }

    /**
     * Get the order from event and create payment.
     *
     * @param GenericEvent $event
     *
     * @throws \InvalidArgumentException
     */
    public function createOrderPayment(GenericEvent $event)
    {
        $this->paymentProcessor->createPayment($this->getOrder($event));
    }

    /**
     * Update order's payment.
     *
     * @param GenericEvent $event
     *
     * @throws \InvalidArgumentException
     */
    public function updateOrderPayment(GenericEvent $event)
    {
        $order = $this->getOrder($event);

        if (!$order->hasPayments()) {
            throw new \InvalidArgumentException('Order payments cannot be empty.');
        }

        /** @var $payment PaymentInterface */
        $payment = $order->getPayments()->last();
        $payment->setCurrency($order->getCurrency());
        $payment->setAmount($order->getTotal());
    }

    /**
     * @param GenericEvent $event
     */
    public function processOrderPaymentCharges(GenericEvent $event)
    {
        $this->paymentChargesProcessor->applyPaymentCharges(
            $this->getOrder($event)
        );
    }

    /**
     * @param GenericEvent $event
     *
     * @return OrderInterface
     *
     * @throws UnexpectedTypeException
     */
    protected function getOrder(GenericEvent $event)
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            throw new UnexpectedTypeException($order, 'Sylius\Component\Core\Model\OrderInterface');
        }

        return $order;
    }
}
