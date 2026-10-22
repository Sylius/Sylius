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
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Core\Promotion\Exception\PromotionUsageLimitReachedException;
use Sylius\Component\Core\Promotion\Modifier\OrderPromotionsUsageModifierInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;

final class AtomicOrderPromotionsUsageModifier implements OrderPromotionsUsageModifierInterface
{
    public function __construct(
        private readonly ?Connection $connection,
        private readonly OrderPromotionsUsageModifierInterface $decoratedModifier,
        private readonly EntityManagerInterface $entityManager,
    ) {
        if (null !== $this->connection) {
            trigger_deprecation(
                'sylius/core-bundle',
                '2.3',
                'Passing a "%s" as the first constructor argument is deprecated and will be prohibited in Sylius 3.0.',
                Connection::class,
            );
        }
    }

    public function increment(OrderInterface $order): void
    {
        foreach ($order->getPromotions() as $promotion) {
            if (!$promotion->isTrackUsage()) {
                continue;
            }

            $this->incrementPromotionUsage($promotion);
        }

        /** @var PromotionCouponInterface|null $coupon */
        $coupon = $order->getPromotionCoupon();
        if (null === $coupon || !$coupon->isTrackUsage()) {
            return;
        }

        $this->incrementCouponUsage($coupon, $order);
    }

    public function decrement(OrderInterface $order): void
    {
        foreach ($order->getPromotions() as $promotion) {
            if (!$promotion->isTrackUsage()) {
                continue;
            }

            $this->decrementPromotionUsage($promotion);
        }

        /** @var PromotionCouponInterface|null $coupon */
        $coupon = $order->getPromotionCoupon();
        if (null === $coupon || !$coupon->isTrackUsage()) {
            return;
        }

        if (OrderInterface::STATE_CANCELLED === $order->getState() && !$coupon->isReusableFromCancelledOrders()) {
            return;
        }

        $this->decrementCouponUsage($coupon);
    }

    private function incrementPromotionUsage(PromotionInterface $promotion): void
    {
        $affected = $this->getConnection()->executeStatement(
            'UPDATE sylius_promotion
             SET used = used + 1
             WHERE id = :id AND (usage_limit IS NULL OR used < usage_limit)',
            ['id' => $promotion->getId()],
        );

        if (0 === $affected) {
            throw PromotionUsageLimitReachedException::withPromotionCode((string) $promotion->getCode());
        }

        $this->entityManager->refresh($promotion);
    }

    private function decrementPromotionUsage(PromotionInterface $promotion): void
    {
        $this->getConnection()->executeStatement(
            'UPDATE sylius_promotion SET used = GREATEST(used - 1, 0) WHERE id = :id',
            ['id' => $promotion->getId()],
        );

        $this->entityManager->refresh($promotion);
    }

    private function incrementCouponUsage(PromotionCouponInterface $coupon, OrderInterface $order): void
    {
        $row = $this->getConnection()->fetchAssociative(
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

        $this->getConnection()->executeStatement(
            'UPDATE sylius_promotion_coupon SET used = used + 1 WHERE id = :id',
            ['id' => $coupon->getId()],
        );

        $this->entityManager->refresh($coupon);
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
        $types = [];

        if ($coupon->isReusableFromCancelledOrders()) {
            $sql .= ' AND o.state != :stateCancelled';
            $params['stateCancelled'] = OrderInterface::STATE_CANCELLED;
        }

        $trackUsageSince = $coupon->getTrackUsageSince();
        if (null !== $trackUsageSince) {
            $sql .= ' AND o.checkout_completed_at >= :since';
            $params['since'] = $trackUsageSince;
            $types['since'] = Types::DATETIME_IMMUTABLE;
        }

        $sql .= ' FOR UPDATE';

        $count = count($this->getConnection()->fetchAllAssociative($sql, $params, $types));

        if ($count >= $perCustomerUsageLimit) {
            throw PromotionUsageLimitReachedException::withCouponCode((string) $coupon->getCode());
        }
    }

    private function decrementCouponUsage(PromotionCouponInterface $coupon): void
    {
        $this->getConnection()->executeStatement(
            'UPDATE sylius_promotion_coupon SET used = GREATEST(used - 1, 0) WHERE id = :id',
            ['id' => $coupon->getId()],
        );

        $this->entityManager->refresh($coupon);
    }

    private function getConnection(): Connection
    {
        return $this->entityManager->getConnection();
    }
}
