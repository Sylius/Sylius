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

namespace Sylius\Bundle\ApiBundle\StateProvider\Admin\Order;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;

/** @implements ProviderInterface<OrderInterface> */
final readonly class CollectionProvider implements ProviderInterface
{
    /**
     * @param ProviderInterface<OrderInterface> $collectionProvider
     * @param OrderRepositoryInterface<OrderInterface> $orderRepository
     */
    public function __construct(
        private ProviderInterface $collectionProvider,
        private OrderRepositoryInterface $orderRepository,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $result = $this->collectionProvider->provide($operation, $uriVariables, $context);

        $this->orderRepository->preloadAdjustments($this->extractOrders($result));

        return $result;
    }

    /**
     * @return OrderInterface[]
     */
    private function extractOrders(mixed $result): array
    {
        if (!is_array($result) && !$result instanceof \Traversable) {
            return [];
        }

        $orders = [];
        foreach ($result as $order) {
            if ($order instanceof OrderInterface) {
                $orders[] = $order;
            }
        }

        return $orders;
    }
}
