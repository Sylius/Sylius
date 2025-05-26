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

namespace Tests\Sylius\Component\Core\Provider;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Provider\ActivePromotionsByChannelProvider;
use Sylius\Component\Core\Repository\PromotionRepositoryInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Promotion\Provider\PreQualifiedPromotionsProviderInterface;
use Sylius\Resource\Exception\UnexpectedTypeException;

final class ActivePromotionsByChannelProviderTest extends TestCase
{
    private MockObject&PromotionRepositoryInterface $promotionRepository;

    private ChannelInterface&MockObject $channel;

    private MockObject&PromotionInterface $firstPromotion;

    private MockObject&PromotionInterface $secondPromotion;

    private MockObject&PromotionCouponInterface $coupon;

    private MockObject&OrderInterface $subject;

    private ActivePromotionsByChannelProvider $provider;

    protected function setUp(): void
    {
        $this->promotionRepository = $this->createMock(PromotionRepositoryInterface::class);
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->firstPromotion = $this->createMock(PromotionInterface::class);
        $this->secondPromotion = $this->createMock(PromotionInterface::class);
        $this->coupon = $this->createMock(PromotionCouponInterface::class);
        $this->subject = $this->createMock(OrderInterface::class);
        $this->provider = new ActivePromotionsByChannelProvider($this->promotionRepository);
    }

    public function testShouldImplementActivePromotionsProviderInterface(): void
    {
        $this->assertInstanceOf(PreQualifiedPromotionsProviderInterface::class, $this->provider);
    }

    public function testShouldProvideActivePromotionsForGivenSubjectChannelWhenNoCouponCodeIsSet(): void
    {
        $this->subject->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->subject->expects($this->once())->method('getPromotionCoupon')->willReturn($this->coupon);
        $this->promotionRepository
            ->expects($this->never())
            ->method('findActiveNonCouponBasedByChannel')
            ->with($this->channel);
        $this->promotionRepository
            ->expects($this->once())
            ->method('findActiveByChannel')
            ->with($this->channel)
            ->willReturn([$this->firstPromotion, $this->secondPromotion]);

        $this->assertSame([$this->firstPromotion, $this->secondPromotion], $this->provider->getPromotions($this->subject));
    }

    public function testShouldProvideActivePromotionsForGivenSubjectChannelWhenCouponCodeIsSet(): void
    {
        $this->subject->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->subject->expects($this->once())->method('getPromotionCoupon')->willReturn(null);

        $this->promotionRepository
            ->expects($this->never())
            ->method('findActiveByChannel')
            ->with($this->channel);
        $this->promotionRepository
            ->expects($this->once())
            ->method('findActiveNonCouponBasedByChannel')
            ->with($this->channel)
            ->willReturn([$this->firstPromotion, $this->secondPromotion]);

        $this->assertSame([$this->firstPromotion, $this->secondPromotion], $this->provider->getPromotions($this->subject));
    }

    public function testShouldThrowExceptionIfOrderHasNoChannel(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->subject->expects($this->once())->method('getChannel')->willReturn(null);

        $this->provider->getPromotions($this->subject);
    }

    public function testShouldThrowExceptionIfPassedSubjectIsNotOrder(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->provider->getPromotions($this->createMock(PromotionSubjectInterface::class));
    }
}
