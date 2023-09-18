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
        LoggerInterface $logger = null,
        private ?ObjectManager $orderManager = null,
        private int $batchSize = 100,
    ) {
        if (null === $logger) {
            trigger_deprecation(
                'sylius/core',
                '1.7',
                'Not passing a logger is deprecated since 1.7',
            );
        }

        if (null === $orderManager) {
            trigger_deprecation(
                'sylius/core',
                '1.13',
                'Not passing an $orderManager is deprecated since 1.13 as it makes $batchSize useless.',
            );
        }

        $this->logger = $logger;
    }

    public function cancel(): void
    {
        $batchSize = null === $this->orderManager ? null : $this->batchSize;

        while ([] !== $expiredUnpaidOrders = $this->findExpiredUnpaidOrders($batchSize)) {
            foreach ($expiredUnpaidOrders as $expiredUnpaidOrder) {
                $this->cancelOrder($expiredUnpaidOrder);
            }

            if (null === $this->orderManager) {
                return;
            }

            $this->orderManager->flush();
            $this->orderManager->clear();
        }
    }

    private function findExpiredUnpaidOrders(?int $batchSize): array
    {
        return $this->orderRepository->findOrdersUnpaidSince(
            new \DateTime('-' . $this->expirationPeriod),
            $batchSize,
        );
    }

    private function cancelOrder(OrderInterface $expiredUnpaidOrder): void
    {
        try {
            $stateMachine = $this->stateMachineFactory->get($expiredUnpaidOrder, OrderTransitions::GRAPH);
            $stateMachine->apply(OrderTransitions::TRANSITION_CANCEL);
        } catch (SMException $e) {
            $this->logger?->error(
                sprintf('An error occurred while cancelling unpaid order #%s', $expiredUnpaidOrder->getId()),
                ['exception' => $e, 'message' => $e->getMessage()],
            );
        }
    }
}
