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
use SM\Factory\FactoryInterface;
use Sylius\Abstraction\StateMachine\Exception\StateMachineExecutionException;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Abstraction\StateMachine\WinzouStateMachineAdapter;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\OrderTransitions;

final class UnpaidOrdersStateUpdater implements UnpaidOrdersStateUpdaterInterface
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private Factory|StateMachineInterface $stateMachineFactory,
        private string $expirationPeriod,
        private ?LoggerInterface $logger = null,
        private ?ObjectManager $orderManager = null,
        private int $batchSize = 100,
    ) {
        if (null === $logger) {
            trigger_deprecation(
                'sylius/core',
                '1.7',
                'Not passing a $logger is deprecated and will be prohibited in Sylius 2.0.',
            );
        }

        if (null === $orderManager) {
            trigger_deprecation(
                'sylius/core',
                '1.13',
                'Not passing the $orderManager is deprecated as it makes $batchSize useless.',
            );
        }

        if ($this->stateMachineFactory instanceof FactoryInterface) {
            trigger_deprecation(
                'sylius/core',
                '1.13',
                sprintf(
                    'Passing an instance of "%s" as the second argument is deprecated. It will accept only instances of "%s" in Sylius 2.0.',
                    FactoryInterface::class,
                    StateMachineInterface::class,
                ),
            );
        }

        $this->logger = $logger;
    }

    public function cancel(?ChannelInterface $channel = null): void
    {
        $batchSize = null === $this->orderManager ? null : $this->batchSize;

        while ([] !== $expiredUnpaidOrders = $this->findExpiredUnpaidOrders($batchSize, $channel)) {
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

    private function findExpiredUnpaidOrders(?int $batchSize, ?ChannelInterface $channel = null): array
    {
        return $this->orderRepository->findOrdersUnpaidSince(
            new \DateTime('-' . $this->expirationPeriod),
            $batchSize,
            $channel,
        );
    }

    private function cancelOrder(OrderInterface $expiredUnpaidOrder): void
    {
        try {
            $stateMachine = $this->getStateMachine();
            $stateMachine->apply($expiredUnpaidOrder, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_CANCEL);
        } catch (StateMachineExecutionException $e) {
            $this->logger?->error(
                sprintf('An error occurred while cancelling unpaid order #%s', $expiredUnpaidOrder->getId()),
                ['exception' => $e, 'message' => $e->getMessage()],
            );
        }
    }

    private function getStateMachine(): StateMachineInterface
    {
        if ($this->stateMachineFactory instanceof FactoryInterface) {
            return new WinzouStateMachineAdapter($this->stateMachineFactory);
        }

        return $this->stateMachineFactory;
    }
}
