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
use spec\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;
use Sylius\Bundle\ApiBundle\Command\Promotion\GeneratePromotionCoupon;
use Sylius\Bundle\ApiBundle\CommandHandler\Promotion\GeneratePromotionCouponHandler;
use Sylius\Bundle\ApiBundle\Exception\PromotionNotFoundException;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Repository\PromotionRepositoryInterface;
use Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInterface;

final class GeneratePromotionCouponHandlerTest extends TestCase
{
    /** @var PromotionRepositoryInterface|MockObject */
    private MockObject $promotionRepositoryMock;

    /** @var PromotionCouponGeneratorInterface|MockObject */
    private MockObject $promotionCouponGeneratorMock;

    private GeneratePromotionCouponHandler $generatePromotionCouponHandler;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        $this->promotionRepositoryMock = $this->createMock(PromotionRepositoryInterface::class);
        $this->promotionCouponGeneratorMock = $this->createMock(PromotionCouponGeneratorInterface::class);
        $this->generatePromotionCouponHandler = new GeneratePromotionCouponHandler($this->promotionRepositoryMock, $this->promotionCouponGeneratorMock);
    }

    public function testThrowsExceptionIfPromotionIsNotFound(): void
    {
        $this->promotionRepositoryMock->expects(self::once())->method('findOneBy')->with(['code' => 'promotion_code'])->willReturn(null);
        $generatePromotionCoupon = new GeneratePromotionCoupon('promotion_code');
        $this->expectException(PromotionNotFoundException::class);
        $this->generatePromotionCouponHandler->__invoke($generatePromotionCoupon);
    }

    public function testGeneratesPromotionCoupons(): void
    {
        /** @var PromotionInterface|MockObject $promotionMock */
        $promotionMock = $this->createMock(PromotionInterface::class);
        /** @var PromotionCouponInterface|MockObject $promotionCouponOneMock */
        $promotionCouponOneMock = $this->createMock(PromotionCouponInterface::class);
        /** @var PromotionCouponInterface|MockObject $promotionCouponTwoMock */
        $promotionCouponTwoMock = $this->createMock(PromotionCouponInterface::class);
        $this->promotionRepositoryMock->expects(self::once())->method('findOneBy')->with(['code' => 'promotion_code'])->willReturn($promotionMock);
        $generatePromotionCoupon = new GeneratePromotionCoupon('promotion_code');
        $this->promotionCouponGeneratorMock->expects(self::once())->method('generate')->with($promotionMock, $generatePromotionCoupon)->willReturn([$promotionCouponOneMock, $promotionCouponTwoMock]);
        $this($generatePromotionCoupon)->shouldIterateAs([$promotionCouponOneMock, $promotionCouponTwoMock]);
    }
}
