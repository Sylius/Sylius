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

namespace Tests\Sylius\Bundle\PromotionBundle\Validator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\PromotionBundle\Validator\Constraints\CouponPossibleGenerationAmount;
use Sylius\Bundle\PromotionBundle\Validator\CouponGenerationAmountValidator;
use Sylius\Component\Promotion\Generator\GenerationPolicyInterface;
use Sylius\Component\Promotion\Generator\ReadablePromotionCouponGeneratorInstructionInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class CouponGenerationAmountValidatorTest extends TestCase
{
    /** @var GenerationPolicyInterface&MockObject */
    private GenerationPolicyInterface $generationPolicy;

    /** @var ExecutionContextInterface&MockObject */
    private ExecutionContextInterface $context;

    private CouponGenerationAmountValidator $couponGenerationAmountValidator;

    /** @var ReadablePromotionCouponGeneratorInstructionInterface&MockObject */
    private ReadablePromotionCouponGeneratorInstructionInterface $instruction;

    private CouponPossibleGenerationAmount $constraint;

    protected function setUp(): void
    {
        parent::setUp();
        $this->generationPolicy = $this->createMock(GenerationPolicyInterface::class);
        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->couponGenerationAmountValidator = new CouponGenerationAmountValidator($this->generationPolicy);
        $this->couponGenerationAmountValidator->initialize($this->context);
        $this->instruction = $this->createMock(ReadablePromotionCouponGeneratorInstructionInterface::class);
        $this->constraint = new CouponPossibleGenerationAmount();
    }

    public function testAddsViolation(): void
    {
        $this->instruction->expects(self::atLeastOnce())->method('getAmount')->willReturn(17);

        $this->instruction->expects(self::atLeastOnce())->method('getCodeLength')->willReturn(1);

        $this->generationPolicy->expects(self::once())
            ->method('isGenerationPossible')
            ->with($this->instruction)
            ->willReturn(false);

        $this->generationPolicy->expects(self::once())
            ->method('getPossibleGenerationAmount')
            ->with($this->instruction);

        $this->context->expects(self::once())->method('addViolation');

        $this->couponGenerationAmountValidator->validate($this->instruction, $this->constraint);
    }

    public function testDoesNotAddViolation(): void
    {
        $this->instruction->expects(self::once())->method('getAmount')->willReturn(5);

        $this->instruction->expects(self::once())->method('getCodeLength')->willReturn(1);

        $this->generationPolicy->expects(self::once())
            ->method('isGenerationPossible')
            ->with($this->instruction)
            ->willReturn(true);

        $this->generationPolicy->expects(self::never())
            ->method('getPossibleGenerationAmount')
            ->with($this->instruction);

        $this->context->expects(self::never())->method('addViolation');

        $this->couponGenerationAmountValidator->validate($this->instruction, $this->constraint);
    }
}
