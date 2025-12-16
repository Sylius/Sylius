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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Promotion\Generator\GenerationPolicyInterface;
use Sylius\Component\Promotion\Generator\PercentageGenerationPolicy;
use Sylius\Component\Promotion\Generator\ReadablePromotionCouponGeneratorInstructionInterface;
use Sylius\Component\Promotion\Repository\PromotionCouponRepositoryInterface;

final class PercentageGenerationPolicyTest extends TestCase
{
    private MockObject&PromotionCouponRepositoryInterface $promotionCouponRepository;

    private MockObject&ReadablePromotionCouponGeneratorInstructionInterface $couponGeneratorInstruction;

    private PercentageGenerationPolicy $generator;

    protected function setUp(): void
    {
        $this->promotionCouponRepository = $this->createMock(PromotionCouponRepositoryInterface::class);
        $this->couponGeneratorInstruction = $this->createMock(ReadablePromotionCouponGeneratorInstructionInterface::class);
        $this->generator = new PercentageGenerationPolicy($this->promotionCouponRepository, 0.5);
    }

    public function testShouldImplementGeneratorValidatorInterface(): void
    {
        $this->assertInstanceOf(GenerationPolicyInterface::class, $this->generator);
    }

    public function testShouldExaminePossibilityOfCouponGeneration(): void
    {
        $this->couponGeneratorInstruction->expects($this->exactly(2))->method('getAmount')->willReturn(17);
        $this->couponGeneratorInstruction->expects($this->once())->method('getCodeLength')->willReturn(1);
        $this->couponGeneratorInstruction->expects($this->once())->method('getPrefix')->willReturn(null);
        $this->couponGeneratorInstruction->expects($this->once())->method('getSuffix')->willReturn(null);
        $this->promotionCouponRepository->expects($this->once())->method('countByCodeLength')->with(1, null, null)->willReturn(0);

        $this->assertFalse($this->generator->isGenerationPossible($this->couponGeneratorInstruction));
    }

    public function testShouldExaminePossibilityOfCouponGenerationWithPrefixAndSuffix(): void
    {
        $this->couponGeneratorInstruction->expects($this->exactly(2))->method('getAmount')->willReturn(7);
        $this->couponGeneratorInstruction->expects($this->once())->method('getCodeLength')->willReturn(1);
        $this->couponGeneratorInstruction->expects($this->once())->method('getPrefix')->willReturn('CHRISTMAS_');
        $this->couponGeneratorInstruction->expects($this->once())->method('getSuffix')->willReturn('_SALE');
        $this->promotionCouponRepository
            ->expects($this->once())
            ->method('countByCodeLength')
            ->with(1, 'CHRISTMAS_', '_SALE')
            ->willReturn(0);

        $this->assertTrue($this->generator->isGenerationPossible($this->couponGeneratorInstruction));
    }

    public function testShouldReturnPossibleGenerationAmount(): void
    {
        $this->couponGeneratorInstruction->expects($this->once())->method('getAmount')->willReturn(17);
        $this->couponGeneratorInstruction->expects($this->once())->method('getCodeLength')->willReturn(1);
        $this->couponGeneratorInstruction->expects($this->once())->method('getPrefix')->willReturn(null);
        $this->couponGeneratorInstruction->expects($this->once())->method('getSuffix')->willReturn(null);
        $this->promotionCouponRepository->expects($this->once())->method('countByCodeLength')->with(1, null, null)->willReturn(1);

        $this->assertSame(7, $this->generator->getPossibleGenerationAmount($this->couponGeneratorInstruction));
    }

    public function testShouldReturnPhpIntMaxValueAsPossibleGenerationAmountWhenCodeLengthIsTooLarge(): void
    {
        $this->couponGeneratorInstruction->expects($this->once())->method('getAmount')->willReturn(1000);
        $this->couponGeneratorInstruction->expects($this->once())->method('getCodeLength')->willReturn(40);
        $this->couponGeneratorInstruction->expects($this->once())->method('getPrefix')->willReturn(null);
        $this->couponGeneratorInstruction->expects($this->once())->method('getSuffix')->willReturn(null);
        $this->promotionCouponRepository->expects($this->once())->method('countByCodeLength')->with(40, null, null)->willReturn(0);

        $this->assertSame(
            \PHP_INT_MAX,
            $this->generator->getPossibleGenerationAmount($this->couponGeneratorInstruction),
        );
    }

    public function testShouldReturnPossibleGenerationAmountWithPrefixAndSuffix(): void
    {
        $this->couponGeneratorInstruction->expects($this->once())->method('getAmount')->willReturn(3);
        $this->couponGeneratorInstruction->expects($this->once())->method('getCodeLength')->willReturn(1);
        $this->couponGeneratorInstruction->expects($this->once())->method('getPrefix')->willReturn('CHRISTMAS_');
        $this->couponGeneratorInstruction->expects($this->once())->method('getSuffix')->willReturn('_SALE');
        $this->promotionCouponRepository
            ->expects($this->once())
            ->method('countByCodeLength')
            ->with(1, 'CHRISTMAS_', '_SALE')
            ->willReturn(5);

        $this->assertSame(3, $this->generator->getPossibleGenerationAmount($this->couponGeneratorInstruction));
    }

    public function testShouldThrowInvalidArgumentExceptionWhenAmountIsNullWhileCheckingIfGenerationIsPossible(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->couponGeneratorInstruction->expects($this->exactly(2))->method('getAmount')->willReturn(null);
        $this->couponGeneratorInstruction->expects($this->once())->method('getCodeLength')->willReturn(1);

        $this->generator->isGenerationPossible($this->couponGeneratorInstruction);
    }

    public function testShouldThrowInvalidArgumentExceptionWhenAmountIsNullWhileGettingPossibleGenerationAmount(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->couponGeneratorInstruction->expects($this->once())->method('getAmount')->willReturn(null);
        $this->couponGeneratorInstruction->expects($this->once())->method('getCodeLength')->willReturn(1);

        $this->generator->getPossibleGenerationAmount($this->couponGeneratorInstruction);
    }

    public function testShouldThrowInvalidArgumentExceptionWhenCodeLengthIsNullWhileCheckingIfGenerationIsPossible(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->couponGeneratorInstruction->expects($this->exactly(2))->method('getAmount')->willReturn(18);
        $this->couponGeneratorInstruction->expects($this->once())->method('getCodeLength')->willReturn(null);

        $this->generator->isGenerationPossible($this->couponGeneratorInstruction);
    }

    public function testShouldThrowInvalidArgumentExceptionWhenCodeLengthIsNullWhileGettingPossibleGenerationAmount(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->couponGeneratorInstruction->expects($this->once())->method('getAmount')->willReturn(18);
        $this->couponGeneratorInstruction->expects($this->once())->method('getCodeLength')->willReturn(null);

        $this->generator->getPossibleGenerationAmount($this->couponGeneratorInstruction);
    }
}
