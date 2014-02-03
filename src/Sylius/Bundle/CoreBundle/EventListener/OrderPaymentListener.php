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
use Sylius\Bundle\CoreBundle\SyliusOrderEvents;
use Sylius\Bundle\PaymentsBundle\Model\PaymentInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
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
     * @var EntityRepository
     */
    protected $orderRepository;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * Constructor.
     *
     * @param PaymentProcessorInterface $paymentProcessor
     * @param EntityRepository          $orderRepository
     * @param EventDispatcherInterface  $dispatcher
     */
    public function __construct(PaymentProcessorInterface $paymentProcessor, EntityRepository $orderRepository, EventDispatcherInterface $dispatcher)
    {
        $this->paymentProcessor = $paymentProcessor;
        $this->orderRepository  = $orderRepository;
        $this->dispatcher       = $dispatcher;
    }

    /**
     * Get the order from event and create payment.
     *
     * @param GenericEvent $event
     */
    public function createOrderPayment(GenericEvent $event)
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            throw new \InvalidArgumentException(
                'Order payment listener requires event subject to be instance of "Sylius\Bundle\CoreBundle\Model\OrderInterface"'
            );
        }

        $this->paymentProcessor->createPayment($order);
    }

    public function updateOrderOnPayment(GenericEvent $event)
    {
        $payment = $event->getSubject();

        if (!$payment instanceof PaymentInterface) {
            throw new \InvalidArgumentException(sprintf(
                'Order payment listener requires event subject to be instance of "Sylius\Bundle\PaymentsBundle\Model\PaymentInterface", %s given.',
                is_object($payment) ? get_class($payment) : gettype($payment)
            ));
        }

        $order = $this->orderRepository->findOneBy(array('payment' => $payment));

        if (null === $order) {
            throw new \Exception(sprintf('Cannot retrieve Order from Payment with id %s', $payment->getId()));
        }

        if (PaymentInterface::STATE_COMPLETED === $payment->getState()) {
            $this->dispatcher->dispatch(SyliusOrderEvents::PRE_PAY, new GenericEvent($order, $event->getArguments()));
            $this->dispatcher->dispatch(SyliusOrderEvents::POST_PAY, new GenericEvent($order, $event->getArguments()));
        }
    }
}
