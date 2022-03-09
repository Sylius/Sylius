<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Updater;

use Psr\Log\LoggerInterface;
use SM\Factory\Factory;
use SM\SMException;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\OrderTransitions;

final class UnpaidOrdersStateUpdater implements UnpaidOrdersStateUpdaterInterface
{
    private ?LoggerInterface $logger;

    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private Factory $stateMachineFactory,
        private string $expirationPeriod,
        LoggerInterface $logger = null
    ) {
        if (null === $logger) {
            @trigger_error(
                'Not passing a logger is deprecated since 1.7',
                \E_USER_DEPRECATED
            );
        }

        $this->logger = $logger;
    }

    public function cancel(): void
    {
        $expiredUnpaidOrders = $this->orderRepository->findOrdersUnpaidSince(new \DateTime('-' . $this->expirationPeriod));
        foreach ($expiredUnpaidOrders as $expiredUnpaidOrder) {
            try {
                $this->cancelOrder($expiredUnpaidOrder);
            } catch (SMException $e) {
                if (null !== $this->logger) {
                    $this->logger->error(
                        sprintf('An error occurred while cancelling unpaid order #%s', $expiredUnpaidOrder->getId()),
                        ['exception' => $e, 'message' => $e->getMessage()]
                    );
                }
            }
        }
    }

    private function cancelOrder(OrderInterface $expiredUnpaidOrder): void
    {
        $stateMachine = $this->stateMachineFactory->get($expiredUnpaidOrder, OrderTransitions::GRAPH);
        $stateMachine->apply(OrderTransitions::TRANSITION_CANCEL);
    }
}
