<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\StateMachineCallback;

use Finite\Factory\FactoryInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Order\OrderTransitions;
use Sylius\Component\Payment\Model\PaymentInterface;

/**
 * Synchronization between payments and their order.
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class OrderPaymentCallback
{
    /**
     * @var EntityRepository
     */
    protected $orderRepository;

    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * @param EntityRepository $orderRepository
     * @param FactoryInterface $factory
     */
    public function __construct(EntityRepository $orderRepository, FactoryInterface $factory)
    {
        $this->orderRepository  = $orderRepository;
        $this->factory          = $factory;
    }

    public function updateOrderOnPayment(PaymentInterface $payment)
    {
        $order = $this->orderRepository->findOneBy(array('payment' => $payment));

        if (null === $order) {
            throw new \RuntimeException(sprintf('Cannot retrieve Order from Payment with id %s', $payment->getId()));
        }

        // When multiple payments support:
        // if total payment === order total

        $this->factory->get($order, OrderTransitions::GRAPH)->apply(OrderTransitions::SYLIUS_CONFIRM);
    }
}
