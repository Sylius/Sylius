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

namespace Tests\Sylius\Component\Promotion\Generator;

use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Promotion\Exception\FailedGenerationException;
use Sylius\Component\Promotion\Generator\GenerationPolicyInterface;
use Sylius\Component\Promotion\Generator\PromotionCouponGenerator;
use Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInterface;
use Sylius\Component\Promotion\Generator\ReadablePromotionCouponGeneratorInstructionInterface;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Repository\PromotionCouponRepositoryInterface;
use Sylius\Resource\Factory\FactoryInterface;

final class PromotionCouponGeneratorTest extends TestCase
{
    /** @var FactoryInterface<PromotionCouponInterface>&MockObject */
    private FactoryInterface&MockObject $promotionCouponFactory;

    private MockObject&PromotionCouponRepositoryInterface $promotionCouponRepository;

    private MockObject&ObjectManager $objectManager;

    private GenerationPolicyInterface&MockObject $generationPolicy;

    private MockObject&PromotionInterface $promotion;

    private MockObject&PromotionCouponInterface $promotionCoupon;

    private MockObject&ReadablePromotionCouponGeneratorInstructionInterface $couponGeneratorInstruction;

    private PromotionCouponGenerator $couponGenerator;

    protected function setUp(): void
    {
        $this->promotionCouponFactory = $this->createMock(FactoryInterface::class);
        $this->promotionCouponRepository = $this->createMock(PromotionCouponRepositoryInterface::class);
        $this->objectManager = $this->createMock(ObjectManager::class);
        $this->generationPolicy = $this->createMock(GenerationPolicyInterface::class);
        $this->promotion = $this->createMock(PromotionInterface::class);
        $this->promotionCoupon = $this->createMock(PromotionCouponInterface::class);
        $this->promotionCouponRepository = $this->createMock(PromotionCouponRepositoryInterface::class);
        $this->couponGeneratorInstruction = $this->createMock(ReadablePromotionCouponGeneratorInstructionInterface::class);
        $this->couponGenerator = new PromotionCouponGenerator(
            $this->promotionCouponFactory,
            $this->promotionCouponRepository,
            $this->objectManager,
            $this->generationPolicy,
        );
    }

    public function testShouldImplementPromotionCouponGeneratorInterface(): void
    {
        $this->assertInstanceOf(PromotionCouponGeneratorInterface::class, $this->couponGenerator);
    }

    public function testShouldGenerateCouponsAccordingToInstruction(): void
    {
        $this->couponGeneratorInstruction->expects($this->once())->method('getAmount')->willReturn(1);
        $this->couponGeneratorInstruction->expects($this->once())->method('getUsageLimit')->willReturn(null);
        $this->couponGeneratorInstruction->expects($this->once())->method('getExpiresAt')->willReturn(null);
        $this->couponGeneratorInstruction->expects($this->once())->method('getPrefix')->willReturn(null);
        $this->couponGeneratorInstruction->expects($this->once())->method('getSuffix')->willReturn(null);
        $this->couponGeneratorInstruction->expects($this->once())->method('getCodeLength')->willReturn(6);
        $this->generationPolicy->expects($this->once())->method('isGenerationPossible')->with($this->couponGeneratorInstruction)->willReturn(true);
        $this->promotionCouponFactory->expects($this->once())->method('createNew')->willReturn($this->promotionCoupon);
        $this->promotionCouponRepository->expects($this->once())->method('findOneBy')->willReturn(null);
        $this->promotionCoupon->expects($this->once())->method('setPromotion')->with($this->promotion);
        $this->promotionCoupon->expects($this->once())->method('setCode');
        $this->promotionCoupon->expects($this->once())->method('setUsageLimit')->with(null);
        $this->promotionCoupon->expects($this->once())->method('setExpiresAt')->with(null);
        $this->objectManager->expects($this->once())->method('persist')->with($this->promotionCoupon);
        $this->objectManager->expects($this->once())->method('flush');

        $this->couponGenerator->generate($this->promotion, $this->couponGeneratorInstruction);
    }

    public function testShouldGenerateCouponsWithPrefixAndSuffixAccordingToInstruction(): void
    {
        $this->couponGeneratorInstruction->expects($this->once())->method('getAmount')->willReturn(1);
        $this->couponGeneratorInstruction->expects($this->once())->method('getUsageLimit')->willReturn(null);
        $this->couponGeneratorInstruction->expects($this->once())->method('getExpiresAt')->willReturn(null);
        $this->couponGeneratorInstruction->expects($this->once())->method('getPrefix')->willReturn('PREFIX_');
        $this->couponGeneratorInstruction->expects($this->once())->method('getSuffix')->willReturn('_SUFFIX');
        $this->couponGeneratorInstruction->expects($this->once())->method('getCodeLength')->willReturn(6);
        $this->generationPolicy->expects($this->once())->method('isGenerationPossible')->with($this->couponGeneratorInstruction)->willReturn(true);
        $this->promotionCouponFactory->expects($this->once())->method('createNew')->willReturn($this->promotionCoupon);
        $this->promotionCouponRepository->expects($this->once())->method('findOneBy')->willReturn(null);
        $this->promotionCoupon->expects($this->once())->method('setPromotion')->with($this->promotion);
        $this->promotionCoupon->expects($this->once())->method('setCode')->willReturnCallback(fn (string $couponCode): bool => str_starts_with($couponCode, 'PREFIX_') &&
        strpos($couponCode, '_SUFFIX') === strlen($couponCode) - strlen('_SUFFIX'));
        $this->promotionCoupon->expects($this->once())->method('setUsageLimit')->with(null);
        $this->promotionCoupon->expects($this->once())->method('setExpiresAt')->with(null);
        $this->objectManager->expects($this->once())->method('persist')->with($this->promotionCoupon);
        $this->objectManager->expects($this->once())->method('flush');

        $this->couponGenerator->generate($this->promotion, $this->couponGeneratorInstruction);
    }

    public function testShouldThrowFailedGenerationExceptionWhenGenerationIsNotPossible(): void
    {
        $this->expectException(FailedGenerationException::class);
        $this->couponGeneratorInstruction->expects($this->once())->method('getAmount')->willReturn(16);
        $this->couponGeneratorInstruction->expects($this->once())->method('getCodeLength')->willReturn(1);
        $this->generationPolicy->expects($this->once())->method('isGenerationPossible')->with($this->couponGeneratorInstruction)->willReturn(false);

        $this->couponGenerator->generate($this->promotion, $this->couponGeneratorInstruction);
    }

    public function testShouldThrowInvalidArgumentExceptionWhenCodeLengthIsLessThanOne(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->couponGeneratorInstruction->expects($this->once())->method('getAmount')->willReturn(16);
        $this->couponGeneratorInstruction->expects($this->once())->method('getCodeLength')->willReturn(0);
        $this->couponGeneratorInstruction->expects($this->once())->method('getPrefix')->willReturn(null);
        $this->couponGeneratorInstruction->expects($this->once())->method('getSuffix')->willReturn(null);
        $this->generationPolicy->expects($this->once())->method('isGenerationPossible')->with($this->couponGeneratorInstruction)->willReturn(true);
        $this->promotionCouponFactory->expects($this->never())->method('createNew');

        $this->couponGenerator->generate($this->promotion, $this->couponGeneratorInstruction);
    }

    public function testShouldThrowInvalidArgumentExceptionWhenCodeLengthIsMoreThanForty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->couponGeneratorInstruction->expects($this->once())->method('getAmount')->willReturn(16);
        $this->couponGeneratorInstruction->expects($this->once())->method('getCodeLength')->willReturn(41);
        $this->couponGeneratorInstruction->expects($this->once())->method('getPrefix')->willReturn(null);
        $this->couponGeneratorInstruction->expects($this->once())->method('getSuffix')->willReturn(null);
        $this->generationPolicy->expects($this->once())->method('isGenerationPossible')->with($this->couponGeneratorInstruction)->willReturn(true);
        $this->promotionCouponFactory->expects($this->never())->method('createNew');

        $this->couponGenerator->generate($this->promotion, $this->couponGeneratorInstruction);
    }
}
