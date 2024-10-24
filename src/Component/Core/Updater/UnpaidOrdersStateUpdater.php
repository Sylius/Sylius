<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Updater;

use Doctrine\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;
use Sylius\Abstraction\StateMachine\Exception\StateMachineExecutionException;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\OrderTransitions;

final class UnpaidOrdersStateUpdater implements UnpaidOrdersStateUpdaterInterface
{
    /** @param OrderRepositoryInterface<OrderInterface> $orderRepository */
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private StateMachineInterface $stateMachine,
        private string $expirationPeriod,
        private LoggerInterface $logger,
        private ObjectManager $orderManager,
        private int $batchSize = 100,
    ) {
    }

    /** @throws \Exception */
    public function cancel(): void
    {
        while ([] !== $expiredUnpaidOrders = $this->findExpiredUnpaidOrders($this->batchSize)) {
            foreach ($expiredUnpaidOrders as $expiredUnpaidOrder) {
                $this->cancelOrder($expiredUnpaidOrder);
            }

            $this->orderManager->flush();
            $this->orderManager->clear();
        }
    }

    /**
     * @return array<OrderInterface>
     *
     * @throws \Exception
     */
    private function findExpiredUnpaidOrders(int $batchSize): array
    {
        return $this->orderRepository->findOrdersUnpaidSince(
            new \DateTime('-' . $this->expirationPeriod),
            $batchSize,
        );
    }

    private function cancelOrder(OrderInterface $expiredUnpaidOrder): void
    {
        try {
            $this->stateMachine->apply($expiredUnpaidOrder, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_CANCEL);
        } catch (StateMachineExecutionException $e) {
            $this->logger->error(
                sprintf('An error occurred while cancelling unpaid order #%s', $expiredUnpaidOrder->getId()),
                ['exception' => $e, 'message' => $e->getMessage()],
            );
        }
    }
}
