<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Inventory\Operator;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class ORMOrderInventoryOperator implements OrderInventoryOperatorInterface
{
    /**
     * @var OrderInventoryOperatorInterface
     */
    private $decoratedOperator;

    /**
     * @var EntityManagerInterface
     */
    private $productVariantManager;

    /**
     * @param OrderInventoryOperatorInterface $decoratedOperator
     * @param EntityManagerInterface $productVariantManager
     */
    public function __construct(
        OrderInventoryOperatorInterface $decoratedOperator,
        EntityManagerInterface $productVariantManager
    ) {
        $this->decoratedOperator = $decoratedOperator;
        $this->productVariantManager = $productVariantManager;
    }

    /**
     * {@inheritdoc}
     *
     * @throws OptimisticLockException
     */
    public function cancel(OrderInterface $order)
    {
        $this->lockProductVariants($order);

        $this->decoratedOperator->cancel($order);
    }

    /**
     * {@inheritdoc}
     *
     * @throws OptimisticLockException
     */
    public function hold(OrderInterface $order)
    {
        $this->lockProductVariants($order);

        $this->decoratedOperator->hold($order);
    }

    /**
     * {@inheritdoc}
     *
     * @throws OptimisticLockException
     */
    public function sell(OrderInterface $order)
    {
        $this->lockProductVariants($order);

        $this->decoratedOperator->sell($order);
    }

    /**
     * @param OrderInterface $order
     *
     * @throws OptimisticLockException
     */
    private function lockProductVariants(OrderInterface $order)
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
