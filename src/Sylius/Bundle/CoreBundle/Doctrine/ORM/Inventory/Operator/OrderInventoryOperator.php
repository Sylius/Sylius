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

namespace Sylius\Bundle\CoreBundle\Doctrine\ORM\Inventory\Operator;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Sylius\Component\Core\Inventory\Operator\OrderInventoryOperatorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;

final class OrderInventoryOperator implements OrderInventoryOperatorInterface
{
    public function __construct(
        private OrderInventoryOperatorInterface $decoratedOperator,
        private EntityManagerInterface $productVariantManager,
    ) {
    }

    /**
     * @throws OptimisticLockException
     */
    public function cancel(OrderInterface $order): void
    {
        $this->lockProductVariants($order);

        $this->decoratedOperator->cancel($order);
    }

    /**
     * @throws OptimisticLockException
     */
    public function hold(OrderInterface $order): void
    {
        $this->lockProductVariants($order);

        $this->decoratedOperator->hold($order);
    }

    /**
     * @throws OptimisticLockException
     */
    public function sell(OrderInterface $order): void
    {
        $this->lockProductVariants($order);

        $this->decoratedOperator->sell($order);
    }

    /**
     * @throws OptimisticLockException
     */
    private function lockProductVariants(OrderInterface $order): void
    {
        /** @var OrderItemInterface $orderItem */
        foreach ($order->getItems() as $orderItem) {
            $variant = $orderItem->getVariant();

            if (!$variant->isTracked()) {
                continue;
            }

            $this->productVariantManager->lock($variant, LockMode::OPTIMISTIC, $variant->getVersion());
        }
    }
}
