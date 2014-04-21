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

use Finite\Factory\FactoryInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderProcessing\PaymentProcessorInterface;
use Sylius\Component\Core\SyliusOrderEvents;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\PaymentTransitions;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
     * @param PaymentProcessorInterface $paymentProcessor
     * @param EventDispatcherInterface  $dispatcher
     * @param FactoryInterface          $factory
     */
    public function __construct(
        PaymentProcessorInterface $paymentProcessor,
        EventDispatcherInterface $dispatcher,
        FactoryInterface $factory
    ) {
        $this->paymentProcessor = $paymentProcessor;
        $this->dispatcher       = $dispatcher;
        $this->factory          = $factory;
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
            throw new \InvalidArgumentException('Order\'s payments cannot be empty.');
        }

        $payment = $order->getPayments()->last();

        $payment->setCurrency($order->getCurrency());
        $payment->setAmount($order->getTotal());
    }

    /**
     * Get the order from event and void payment.
     *
     * @param GenericEvent $event
     *
     * @throws UnexpectedTypeException
     */
    public function voidOrderPayment(GenericEvent $event)
    {
        $payment = $this->getOrder($event)->getPayments()->last();
        $this->factory->get($payment, PaymentTransitions::GRAPH)->apply(PaymentTransitions::SYLIUS_VOID);
    }

    public function updateOrderOnPayment(GenericEvent $event)
    {
        $payment = $event->getSubject();

        if (!$payment instanceof PaymentInterface) {
            throw new UnexpectedTypeException(
                $payment,
                'Sylius\Component\Payment\Model\PaymentInterface'
            );
        }

        $order = $payment->getOrder();

        if (null === $order) {
            throw new \RuntimeException(sprintf('Cannot retrieve Order from Payment with id %s', $payment->getId()));
        }

        if (PaymentInterface::STATE_COMPLETED === $payment->getState()) {
            $this->dispatcher->dispatch(SyliusOrderEvents::PRE_PAY, new GenericEvent($order, $event->getArguments()));
            $this->dispatcher->dispatch(SyliusOrderEvents::POST_PAY, new GenericEvent($order, $event->getArguments()));
        }
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
