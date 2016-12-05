<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Updater;

use SM\Factory\Factory;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\OrderTransitions;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class UnpaidOrdersStateUpdater implements UnpaidOrdersStateUpdaterInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var Factory
     */
    private $stateMachineFactory;

    /**
     * @var string
     */
    private $expirationPeriod;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param Factory $stateMachineFactory
     * @param string $expirationPeriod
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        Factory $stateMachineFactory,
        $expirationPeriod
    ) {
        $this->orderRepository = $orderRepository;
        $this->stateMachineFactory = $stateMachineFactory;
        $this->expirationPeriod = $expirationPeriod;
    }

    public function cancel()
    {
        $expiredUnpaidOrders = $this->orderRepository->findOrdersUnpaidSince(new \DateTime('-'.$this->expirationPeriod));
        foreach ($expiredUnpaidOrders as $expiredUnpaidOrder) {
            $this->cancelOrder($expiredUnpaidOrder);
        }
    }

    /**
     * @param OrderInterface $expiredUnpaidOrder
     */
    private function cancelOrder(OrderInterface $expiredUnpaidOrder)
    {
        $stateMachine = $this->stateMachineFactory->get($expiredUnpaidOrder, OrderTransitions::GRAPH);
        $stateMachine->apply(OrderTransitions::TRANSITION_CANCEL);
    }
}
