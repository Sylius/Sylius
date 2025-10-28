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

namespace Tests\Sylius\Bundle\ApiBundle\CommandHandler\Promotion;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Command\Promotion\GeneratePromotionCoupon;
use Sylius\Bundle\ApiBundle\CommandHandler\Promotion\GeneratePromotionCouponHandler;
use Sylius\Bundle\ApiBundle\Exception\PromotionNotFoundException;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Repository\PromotionRepositoryInterface;
use Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInterface;
use Tests\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;

final class GeneratePromotionCouponHandlerTest extends TestCase
{
    private MockObject&PromotionRepositoryInterface $promotionRepository;

    private MockObject&PromotionCouponGeneratorInterface $promotionCouponGenerator;

    private GeneratePromotionCouponHandler $handler;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        $this->promotionRepository = $this->createMock(PromotionRepositoryInterface::class);
        $this->promotionCouponGenerator = $this->createMock(PromotionCouponGeneratorInterface::class);
        $this->handler = new GeneratePromotionCouponHandler($this->promotionRepository, $this->promotionCouponGenerator);
    }

    public function testThrowsExceptionIfPromotionIsNotFound(): void
    {
        $this->promotionRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['code' => 'promotion_code'])
            ->willReturn(null);
        $generatePromotionCoupon = new GeneratePromotionCoupon('promotion_code');
        self::expectException(PromotionNotFoundException::class);
        $this->handler->__invoke($generatePromotionCoupon);
    }

    public function testGeneratesPromotionCoupons(): void
    {
        /** @var PromotionInterface|MockObject $promotion */
        $promotion = $this->createMock(PromotionInterface::class);
        /** @var PromotionCouponInterface|MockObject $promotionCouponOne */
        $promotionCouponOne = $this->createMock(PromotionCouponInterface::class);
        /** @var PromotionCouponInterface|MockObject $promotionCouponTwo */
        $promotionCouponTwo = $this->createMock(PromotionCouponInterface::class);
        $this->promotionRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['code' => 'promotion_code'])
            ->willReturn($promotion);
        $generatePromotionCoupon = new GeneratePromotionCoupon('promotion_code');
        $this->promotionCouponGenerator->expects(self::once())
            ->method('generate')
            ->with($promotion, $generatePromotionCoupon)
            ->willReturn([$promotionCouponOne, $promotionCouponTwo]);
        self::assertSame(
            [$promotionCouponOne, $promotionCouponTwo],
            iterator_to_array($this->handler->__invoke($generatePromotionCoupon)),
        );
    }
}
