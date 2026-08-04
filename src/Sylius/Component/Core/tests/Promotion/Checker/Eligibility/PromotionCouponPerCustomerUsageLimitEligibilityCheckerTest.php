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

namespace Tests\Sylius\Component\Core\Promotion\Checker\Eligibility;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface as CorePromotionCouponInterface;
use Sylius\Component\Core\Promotion\Checker\Eligibility\PromotionCouponPerCustomerUsageLimitEligibilityChecker;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionCouponEligibilityCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

#[AllowMockObjectsWithoutExpectations]
final class PromotionCouponPerCustomerUsageLimitEligibilityCheckerTest extends TestCase
{
    private MockObject&OrderRepositoryInterface $orderRepository;

    private MockObject&OrderInterface $promotionSubject;

    private CorePromotionCouponInterface&MockObject $promotionCoupon;

    private CustomerInterface&MockObject $customer;

    private PromotionCouponPerCustomerUsageLimitEligibilityChecker $checker;

    protected function setUp(): void
    {
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->promotionSubject = $this->createMock(OrderInterface::class);
        $this->promotionCoupon = $this->createMock(CorePromotionCouponInterface::class);
        $this->customer = $this->createMock(CustomerInterface::class);
        $this->checker = new PromotionCouponPerCustomerUsageLimitEligibilityChecker($this->orderRepository);
    }

    public function testShouldImplementPromotionCouponEligibilityCheckerInterface(): void
    {
        $this->assertInstanceOf(PromotionCouponEligibilityCheckerInterface::class, $this->checker);
    }

    public function testShouldReturnFalseIfPromotionCouponHasReachedItsPerCustomerUsageLimit(): void
    {
        $since = new \DateTimeImmutable();
        $this->customer->expects($this->once())->method('getId')->willReturn(1);
        $this->promotionSubject->expects($this->once())->method('getCustomer')->willReturn($this->customer);
        $this->promotionCoupon->expects($this->once())->method('getPerCustomerUsageLimit')->willReturn(42);
        $this->promotionCoupon->method('isTrackUsage')->willReturn(true);
        $this->promotionCoupon->method('getTrackUsageSince')->willReturn($since);
        $this->orderRepository
            ->expects($this->once())
            ->method('countByCustomerAndCouponSince')
            ->with($this->customer, $this->promotionCoupon, $since)
            ->willReturn(42);

        $this->assertFalse($this->checker->isEligible($this->promotionSubject, $this->promotionCoupon));
    }

    public function testShouldReturnTrueIfPromotionCouponHasNotReachedItsPerCustomerUsageLimit(): void
    {
        $since = new \DateTimeImmutable();
        $this->customer->expects($this->once())->method('getId')->willReturn(1);
        $this->promotionSubject->expects($this->once())->method('getCustomer')->willReturn($this->customer);
        $this->promotionCoupon->expects($this->once())->method('getPerCustomerUsageLimit')->willReturn(42);
        $this->promotionCoupon->method('isTrackUsage')->willReturn(true);
        $this->promotionCoupon->method('getTrackUsageSince')->willReturn($since);
        $this->orderRepository
            ->expects($this->once())
            ->method('countByCustomerAndCouponSince')
            ->with($this->customer, $this->promotionCoupon, $since)
            ->willReturn(41);

        $this->assertTrue($this->checker->isEligible($this->promotionSubject, $this->promotionCoupon));
    }

    public function testShouldReturnTrueIfPromotionCouponHasTrackUsageDisabled(): void
    {
        $this->promotionCoupon->expects($this->once())->method('getPerCustomerUsageLimit')->willReturn(42);
        $this->promotionCoupon->method('isTrackUsage')->willReturn(false);

        $this->assertTrue($this->checker->isEligible($this->promotionSubject, $this->promotionCoupon));
    }

    public function testShouldReturnTrueIfPromotionSubjectHasCustomerThatIsNotPersisted(): void
    {
        $this->customer->expects($this->once())->method('getId')->willReturn(null);
        $this->promotionSubject->expects($this->once())->method('getCustomer')->willReturn($this->customer);
        $this->promotionCoupon->expects($this->once())->method('getPerCustomerUsageLimit')->willReturn(42);
        $this->promotionCoupon->method('isTrackUsage')->willReturn(true);

        $this->assertTrue($this->checker->isEligible($this->promotionSubject, $this->promotionCoupon));
    }

    public function testShouldReturnTrueIfPromotionSubjectHasNoCustomer(): void
    {
        $this->promotionSubject->expects($this->once())->method('getCustomer')->willReturn(null);
        $this->promotionCoupon->expects($this->once())->method('getPerCustomerUsageLimit')->willReturn(42);
        $this->promotionCoupon->method('isTrackUsage')->willReturn(true);

        $this->assertTrue($this->checker->isEligible($this->promotionSubject, $this->promotionCoupon));
    }

    public function testShouldNotCountOrdersPlacedBeforeTrackUsageWasEnabled(): void
    {
        $since = new \DateTimeImmutable('2026-06-16 12:00:00');
        $this->customer->expects($this->once())->method('getId')->willReturn(1);
        $this->promotionSubject->expects($this->once())->method('getCustomer')->willReturn($this->customer);
        $this->promotionCoupon->expects($this->once())->method('getPerCustomerUsageLimit')->willReturn(2);
        $this->promotionCoupon->method('isTrackUsage')->willReturn(true);
        $this->promotionCoupon->method('getTrackUsageSince')->willReturn($since);
        $this->orderRepository
            ->expects($this->once())
            ->method('countByCustomerAndCouponSince')
            ->with($this->customer, $this->promotionCoupon, $since)
            ->willReturn(0);

        $this->assertTrue($this->checker->isEligible($this->promotionSubject, $this->promotionCoupon));
    }

    public function testShouldResetCountAfterTrackUsageIsReenabled(): void
    {
        $since = new \DateTimeImmutable('2026-06-16 15:00:00');
        $this->customer->expects($this->once())->method('getId')->willReturn(1);
        $this->promotionSubject->expects($this->once())->method('getCustomer')->willReturn($this->customer);
        $this->promotionCoupon->expects($this->once())->method('getPerCustomerUsageLimit')->willReturn(2);
        $this->promotionCoupon->method('isTrackUsage')->willReturn(true);
        $this->promotionCoupon->method('getTrackUsageSince')->willReturn($since);
        $this->orderRepository
            ->expects($this->once())
            ->method('countByCustomerAndCouponSince')
            ->with($this->customer, $this->promotionCoupon, $since)
            ->willReturn(1);

        $this->assertTrue($this->checker->isEligible($this->promotionSubject, $this->promotionCoupon));
    }

    public function testShouldReturnTrueIfPromotionCouponHasNoPerCustomerUsageLimit(): void
    {
        $this->promotionCoupon->expects($this->once())->method('getPerCustomerUsageLimit')->willReturn(null);

        $this->assertTrue($this->checker->isEligible($this->promotionSubject, $this->promotionCoupon));
    }

    public function testShouldReturnTrueIfPromotionCouponIsNotCoreOne(): void
    {
        $this->assertTrue(
            $this->checker->isEligible(
                $this->promotionSubject,
                $this->createMock(PromotionCouponInterface::class),
            ),
        );
    }

    public function testShouldReturnTrueIfPromotionSubjectIsNotCoreOrder(): void
    {
        $this->assertTrue(
            $this->checker->isEligible(
                $this->createMock(PromotionSubjectInterface::class),
                $this->promotionCoupon,
            ),
        );
    }
}
