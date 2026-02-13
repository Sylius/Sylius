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

namespace Sylius\Bundle\CoreBundle\Doctrine\ORM\Promotion\Modifier;

use Doctrine\DBAL\Connection;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Core\Promotion\Exception\PromotionUsageLimitReachedException;
use Sylius\Component\Core\Promotion\Modifier\OrderPromotionsUsageModifierInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;

final class AtomicOrderPromotionsUsageModifier implements OrderPromotionsUsageModifierInterface
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function increment(OrderInterface $order): void
    {
        foreach ($order->getPromotions() as $promotion) {
            $this->incrementPromotionUsage($promotion);
        }

        /** @var PromotionCouponInterface|null $coupon */
        $coupon = $order->getPromotionCoupon();
        if (null === $coupon) {
            return;
        }

        $this->incrementCouponUsage($coupon, $order);
    }

    public function decrement(OrderInterface $order): void
    {
        foreach ($order->getPromotions() as $promotion) {
            $this->decrementPromotionUsage($promotion);
        }

        /** @var PromotionCouponInterface|null $coupon */
        $coupon = $order->getPromotionCoupon();
        if (null === $coupon) {
            return;
        }

        if (OrderInterface::STATE_CANCELLED === $order->getState() && !$coupon->isReusableFromCancelledOrders()) {
            return;
        }

        $this->decrementCouponUsage($coupon);
    }

    private function incrementPromotionUsage(PromotionInterface $promotion): void
    {
        $affected = $this->connection->executeStatement(
            'UPDATE sylius_promotion
             SET used = used + 1
             WHERE id = :id AND (usage_limit IS NULL OR used < usage_limit)',
            ['id' => $promotion->getId()],
        );

        if (0 === $affected) {
            throw PromotionUsageLimitReachedException::withPromotionCode((string) $promotion->getCode());
        }

        $newUsed = (int) $this->connection->fetchOne(
            'SELECT used FROM sylius_promotion WHERE id = :id',
            ['id' => $promotion->getId()],
        );

        $promotion->setUsed($newUsed);
    }

    private function decrementPromotionUsage(PromotionInterface $promotion): void
    {
        $this->connection->executeStatement(
            'UPDATE sylius_promotion SET used = GREATEST(used - 1, 0) WHERE id = :id',
            ['id' => $promotion->getId()],
        );

        $newUsed = (int) $this->connection->fetchOne(
            'SELECT used FROM sylius_promotion WHERE id = :id',
            ['id' => $promotion->getId()],
        );

        $promotion->setUsed($newUsed);
    }

    private function incrementCouponUsage(PromotionCouponInterface $coupon, OrderInterface $order): void
    {
        $row = $this->connection->fetchAssociative(
            'SELECT used, usage_limit, per_customer_usage_limit FROM sylius_promotion_coupon WHERE id = :id FOR UPDATE',
            ['id' => $coupon->getId()],
        );

        if (false === $row) {
            throw PromotionUsageLimitReachedException::withCouponCode((string) $coupon->getCode());
        }

        if (null !== $row['usage_limit'] && (int) $row['used'] >= (int) $row['usage_limit']) {
            throw PromotionUsageLimitReachedException::withCouponCode((string) $coupon->getCode());
        }

        if (null !== $row['per_customer_usage_limit']) {
            $this->assertPerCustomerCouponUsageLimitNotReached(
                $coupon,
                $order,
                (int) $row['per_customer_usage_limit'],
            );
        }

        $this->connection->executeStatement(
            'UPDATE sylius_promotion_coupon SET used = used + 1 WHERE id = :id',
            ['id' => $coupon->getId()],
        );

        $coupon->setUsed((int) $row['used'] + 1);
    }

    private function assertPerCustomerCouponUsageLimitNotReached(
        PromotionCouponInterface $coupon,
        OrderInterface $order,
        int $perCustomerUsageLimit,
    ): void {
        $customer = $order->getCustomer();
        if (null === $customer || null === $customer->getId()) {
            return;
        }

        $sql = 'SELECT o.id FROM sylius_order o
                WHERE o.customer_id = :customerId
                AND o.promotion_coupon_id = :couponId
                AND o.state != :stateCart';
        $params = [
            'customerId' => $customer->getId(),
            'couponId' => $coupon->getId(),
            'stateCart' => OrderInterface::STATE_CART,
        ];

        if ($coupon->isReusableFromCancelledOrders()) {
            $sql .= ' AND o.state != :stateCancelled';
            $params['stateCancelled'] = OrderInterface::STATE_CANCELLED;
        }

        $sql .= ' FOR UPDATE';

        $count = count($this->connection->fetchAllAssociative($sql, $params));

        if ($count >= $perCustomerUsageLimit) {
            throw PromotionUsageLimitReachedException::withCouponCode((string) $coupon->getCode());
        }
    }

    private function decrementCouponUsage(PromotionCouponInterface $coupon): void
    {
        $this->connection->executeStatement(
            'UPDATE sylius_promotion_coupon SET used = GREATEST(used - 1, 0) WHERE id = :id',
            ['id' => $coupon->getId()],
        );

        $newUsed = (int) $this->connection->fetchOne(
            'SELECT used FROM sylius_promotion_coupon WHERE id = :id',
            ['id' => $coupon->getId()],
        );

        $coupon->setUsed($newUsed);
    }
}
