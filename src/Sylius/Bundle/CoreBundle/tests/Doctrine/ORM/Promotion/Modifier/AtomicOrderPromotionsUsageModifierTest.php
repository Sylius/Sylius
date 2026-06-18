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
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\Promotion\Modifier\AtomicOrderPromotionsUsageModifier;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Promotion\Modifier\OrderPromotionsUsageModifierInterface;

final class AtomicOrderPromotionsUsageModifierTest extends TestCase
{
    private MockObject&OrderPromotionsUsageModifierInterface $decoratedModifier;

    private EntityManagerInterface&MockObject $entityManager;

    private AtomicOrderPromotionsUsageModifier $atomicModifier;

    protected function setUp(): void
    {
        $this->decoratedModifier = $this->createMock(OrderPromotionsUsageModifierInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->atomicModifier = new AtomicOrderPromotionsUsageModifier(
            null,
            $this->decoratedModifier,
            $this->entityManager,
        );
    }

    public function testLocksPromotionsAndCouponDuringIncrement(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $promotion = $this->createMock(PromotionInterface::class);
        $coupon = $this->createMock(PromotionCouponInterface::class);

        $order->method('getPromotions')->willReturn(new ArrayCollection([$promotion]));
        $order->method('getPromotionCoupon')->willReturn($coupon);

        $promotion->method('getVersion')->willReturn(3);
        $coupon->method('getVersion')->willReturn(5);

        $this->entityManager
            ->expects($this->exactly(2))
            ->method('refresh')
            ->willReturnCallback(function (object $entity) use ($promotion, $coupon): void {
                $this->assertTrue($entity === $promotion || $entity === $coupon);
            })
        ;

        $this->entityManager
            ->expects($this->exactly(2))
            ->method('lock')
            ->willReturnCallback(function (object $entity, int|LockMode $lockMode, mixed $version) use ($promotion, $coupon): void {
                $this->assertSame(LockMode::OPTIMISTIC, $lockMode);

                if ($entity === $promotion) {
                    $this->assertSame(3, $version);
                } elseif ($entity === $coupon) {
                    $this->assertSame(5, $version);
                } else {
                    $this->fail('Unexpected entity locked');
                }
            })
        ;

        $this->decoratedModifier->expects($this->once())->method('increment')->with($order);

        $this->atomicModifier->increment($order);
    }

    public function testLocksPromotionsDuringIncrementWithoutCoupon(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $promotion = $this->createMock(PromotionInterface::class);

        $order->method('getPromotions')->willReturn(new ArrayCollection([$promotion]));
        $order->method('getPromotionCoupon')->willReturn(null);

        $promotion->method('getVersion')->willReturn(1);

        $this->entityManager
            ->expects($this->once())
            ->method('refresh')
            ->with($promotion)
        ;

        $this->entityManager
            ->expects($this->once())
            ->method('lock')
            ->with($promotion, LockMode::OPTIMISTIC, 1)
        ;

        $this->decoratedModifier->expects($this->once())->method('increment')->with($order);

        $this->atomicModifier->increment($order);
    }

    public function testLocksPromotionsAndCouponDuringDecrement(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $promotion = $this->createMock(PromotionInterface::class);
        $coupon = $this->createMock(PromotionCouponInterface::class);

        $order->method('getPromotions')->willReturn(new ArrayCollection([$promotion]));
        $order->method('getPromotionCoupon')->willReturn($coupon);

        $promotion->method('getVersion')->willReturn(3);
        $coupon->method('getVersion')->willReturn(5);

        $this->entityManager
            ->expects($this->exactly(2))
            ->method('refresh')
            ->willReturnCallback(function (object $entity) use ($promotion, $coupon): void {
                $this->assertTrue($entity === $promotion || $entity === $coupon);
            })
        ;

        $this->entityManager
            ->expects($this->exactly(2))
            ->method('lock')
            ->willReturnCallback(function (object $entity, int|LockMode $lockMode, mixed $version) use ($promotion, $coupon): void {
                $this->assertSame(LockMode::OPTIMISTIC, $lockMode);

                if ($entity === $promotion) {
                    $this->assertSame(3, $version);
                } elseif ($entity === $coupon) {
                    $this->assertSame(5, $version);
                } else {
                    $this->fail('Unexpected entity locked');
                }
            })
        ;

        $this->decoratedModifier->expects($this->once())->method('decrement')->with($order);

        $this->atomicModifier->decrement($order);
    }

    public function testLocksPromotionsDuringDecrementWithoutCoupon(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $promotion = $this->createMock(PromotionInterface::class);

        $order->method('getPromotions')->willReturn(new ArrayCollection([$promotion]));
        $order->method('getPromotionCoupon')->willReturn(null);

        $promotion->method('getVersion')->willReturn(1);

        $this->entityManager
            ->expects($this->once())
            ->method('refresh')
            ->with($promotion)
        ;

        $this->entityManager
            ->expects($this->once())
            ->method('lock')
            ->with($promotion, LockMode::OPTIMISTIC, 1)
        ;

        $this->decoratedModifier->expects($this->once())->method('decrement')->with($order);

        $this->atomicModifier->decrement($order);
    }

    public function testLocksMultiplePromotionsDuringIncrement(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $firstPromotion = $this->createMock(PromotionInterface::class);
        $secondPromotion = $this->createMock(PromotionInterface::class);

        $order->method('getPromotions')->willReturn(new ArrayCollection([$firstPromotion, $secondPromotion]));
        $order->method('getPromotionCoupon')->willReturn(null);

        $firstPromotion->method('getVersion')->willReturn(2);
        $secondPromotion->method('getVersion')->willReturn(4);

        $this->entityManager
            ->expects($this->exactly(2))
            ->method('refresh')
            ->willReturnCallback(function (object $entity) use ($firstPromotion, $secondPromotion): void {
                $this->assertTrue($entity === $firstPromotion || $entity === $secondPromotion);
            })
        ;

        $this->entityManager
            ->expects($this->exactly(2))
            ->method('lock')
            ->willReturnCallback(function (object $entity, int|LockMode $lockMode, mixed $version) use ($firstPromotion, $secondPromotion): void {
                $this->assertSame(LockMode::OPTIMISTIC, $lockMode);

                if ($entity === $firstPromotion) {
                    $this->assertSame(2, $version);
                } elseif ($entity === $secondPromotion) {
                    $this->assertSame(4, $version);
                } else {
                    $this->fail('Unexpected entity locked');
                }
            })
        ;

        $this->decoratedModifier->expects($this->once())->method('increment')->with($order);

        $this->atomicModifier->increment($order);
    }
}
