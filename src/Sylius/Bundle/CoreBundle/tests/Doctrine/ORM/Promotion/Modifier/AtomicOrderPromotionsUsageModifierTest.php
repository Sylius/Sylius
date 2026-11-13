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

namespace Tests\Sylius\Bundle\CoreBundle\Doctrine\ORM\Promotion\Modifier;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\Promotion\Modifier\AtomicOrderPromotionsUsageModifier;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Promotion\Exception\PromotionUsageLimitReachedException;
use Sylius\Component\Core\Promotion\Modifier\OrderPromotionsUsageModifierInterface;

final class AtomicOrderPromotionsUsageModifierTest extends TestCase
{
    private Connection&MockObject $connection;

    private MockObject&OrderPromotionsUsageModifierInterface $decoratedModifier;

    private EntityManagerInterface&MockObject $entityManager;

    private AtomicOrderPromotionsUsageModifier $atomicModifier;

    protected function setUp(): void
    {
        $this->connection = $this->createMock(Connection::class);
        $this->decoratedModifier = $this->createMock(OrderPromotionsUsageModifierInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->entityManager->method('getConnection')->willReturn($this->connection);

        $this->atomicModifier = new AtomicOrderPromotionsUsageModifier(
            null,
            $this->decoratedModifier,
            $this->entityManager,
        );
    }

    public function testIncrementsPromotionUsageWithConditionalUpdate(): void
    {
        $promotion = $this->createPromotion(1, true);
        $order = $this->createOrder([$promotion]);

        $this->connection
            ->expects($this->once())
            ->method('executeStatement')
            ->with($this->stringContains('used < usage_limit'), ['id' => 1])
            ->willReturn(1)
        ;
        $this->entityManager->expects($this->once())->method('refresh')->with($promotion);
        $this->decoratedModifier->expects($this->never())->method('increment');

        $this->atomicModifier->increment($order);
    }

    public function testThrowsExceptionWhenPromotionUsageLimitIsReached(): void
    {
        $promotion = $this->createPromotion(1, true);
        $order = $this->createOrder([$promotion]);

        $this->connection->method('executeStatement')->willReturn(0);
        $this->entityManager->expects($this->never())->method('refresh');

        $this->expectException(PromotionUsageLimitReachedException::class);

        $this->atomicModifier->increment($order);
    }

    public function testDoesNotIncrementPromotionUsageWithTrackUsageDisabled(): void
    {
        $order = $this->createOrder([$this->createPromotion(1, false)]);

        $this->connection->expects($this->never())->method('executeStatement');
        $this->entityManager->expects($this->never())->method('refresh');

        $this->atomicModifier->increment($order);
    }

    public function testIncrementsCouponUsageUnderRowLock(): void
    {
        $coupon = $this->createCoupon(7, true);
        $order = $this->createOrder([], $coupon);

        $this->connection
            ->expects($this->once())
            ->method('fetchAssociative')
            ->with($this->stringContains('FOR UPDATE'), ['id' => 7])
            ->willReturn(['used' => 0, 'usage_limit' => 1, 'per_customer_usage_limit' => null])
        ;
        $this->connection
            ->expects($this->once())
            ->method('executeStatement')
            ->with($this->stringContains('used = used + 1'), ['id' => 7])
            ->willReturn(1)
        ;
        $this->entityManager->expects($this->once())->method('refresh')->with($coupon);

        $this->atomicModifier->increment($order);
    }

    public function testThrowsExceptionWhenCouponUsageLimitIsReached(): void
    {
        $order = $this->createOrder([], $this->createCoupon(7, true));

        $this->connection
            ->method('fetchAssociative')
            ->willReturn(['used' => 1, 'usage_limit' => 1, 'per_customer_usage_limit' => null])
        ;
        $this->connection->expects($this->never())->method('executeStatement');

        $this->expectException(PromotionUsageLimitReachedException::class);

        $this->atomicModifier->increment($order);
    }

    public function testThrowsExceptionWhenCouponPerCustomerUsageLimitIsReached(): void
    {
        $customer = $this->createMock(CustomerInterface::class);
        $customer->method('getId')->willReturn(3);
        $order = $this->createOrder([], $this->createCoupon(7, true), $customer);

        $this->connection
            ->method('fetchAssociative')
            ->willReturn(['used' => 1, 'usage_limit' => null, 'per_customer_usage_limit' => 1])
        ;
        $this->connection
            ->expects($this->once())
            ->method('fetchAllAssociative')
            ->with($this->stringContains('FOR UPDATE'), $this->callback(
                static fn (array $params): bool => 3 === $params['customerId'] && 7 === $params['couponId'],
            ))
            ->willReturn([['id' => 10]])
        ;
        $this->connection->expects($this->never())->method('executeStatement');

        $this->expectException(PromotionUsageLimitReachedException::class);

        $this->atomicModifier->increment($order);
    }

    public function testDoesNotIncrementCouponUsageWithTrackUsageDisabled(): void
    {
        $order = $this->createOrder([], $this->createCoupon(7, false));

        $this->connection->expects($this->never())->method('fetchAssociative');
        $this->connection->expects($this->never())->method('executeStatement');

        $this->atomicModifier->increment($order);
    }

    public function testDecrementsPromotionAndCouponUsage(): void
    {
        $promotion = $this->createPromotion(1, true);
        $coupon = $this->createCoupon(7, true);
        $order = $this->createOrder([$promotion], $coupon);

        $this->connection
            ->expects($this->exactly(2))
            ->method('executeStatement')
            ->with($this->stringContains('GREATEST(used - 1, 0)'))
            ->willReturn(1)
        ;
        $this->entityManager->expects($this->exactly(2))->method('refresh');
        $this->decoratedModifier->expects($this->never())->method('decrement');

        $this->atomicModifier->decrement($order);
    }

    public function testDoesNotDecrementCouponUsageForCancelledOrderWhenCouponIsNotReusable(): void
    {
        $coupon = $this->createCoupon(7, true);
        $coupon->method('isReusableFromCancelledOrders')->willReturn(false);
        $order = $this->createOrder([], $coupon);
        $order->method('getState')->willReturn(OrderInterface::STATE_CANCELLED);

        $this->connection->expects($this->never())->method('executeStatement');

        $this->atomicModifier->decrement($order);
    }

    private function createPromotion(int $id, bool $trackUsage): MockObject&PromotionInterface
    {
        $promotion = $this->createMock(PromotionInterface::class);
        $promotion->method('getId')->willReturn($id);
        $promotion->method('getCode')->willReturn('PROMOTION_' . $id);
        $promotion->method('isTrackUsage')->willReturn($trackUsage);

        return $promotion;
    }

    private function createCoupon(int $id, bool $trackUsage): MockObject&PromotionCouponInterface
    {
        $coupon = $this->createMock(PromotionCouponInterface::class);
        $coupon->method('getId')->willReturn($id);
        $coupon->method('getCode')->willReturn('COUPON_' . $id);
        $coupon->method('isTrackUsage')->willReturn($trackUsage);

        return $coupon;
    }

    /** @param PromotionInterface[] $promotions */
    private function createOrder(
        array $promotions,
        ?PromotionCouponInterface $coupon = null,
        ?CustomerInterface $customer = null,
    ): MockObject&OrderInterface {
        $order = $this->createMock(OrderInterface::class);
        $order->method('getPromotions')->willReturn(new ArrayCollection($promotions));
        $order->method('getPromotionCoupon')->willReturn($coupon);
        $order->method('getCustomer')->willReturn($customer);

        return $order;
    }
}
