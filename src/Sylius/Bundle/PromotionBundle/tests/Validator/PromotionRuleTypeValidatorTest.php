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
use Sylius\Bundle\PromotionBundle\Validator\Constraints\PromotionRuleType;
use Sylius\Bundle\PromotionBundle\Validator\PromotionRuleTypeValidator;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class PromotionRuleTypeValidatorTest extends TestCase
{
    /** @var ExecutionContextInterface&MockObject */
    private MockObject $context;

    private PromotionRuleTypeValidator $promotionRuleTypeValidator;

    /** @var PromotionRuleInterface&MockObject */
    private PromotionRuleInterface $promotionRule;

    protected function setUp(): void
    {
        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->promotionRuleTypeValidator = new PromotionRuleTypeValidator(['rule_one' => 'rule_one', 'rule_two' => 'rule_two']);
        $this->promotionRuleTypeValidator->initialize($this->context);
        $this->promotionRule = $this->createMock(PromotionRuleInterface::class);
    }

    public function testThrowsAnExceptionIfConstraintIsNotAnInstanceOfPromotionRuleType(): void
    {
        /** @var Constraint&MockObject $constraint */
        $constraint = $this->createMock(Constraint::class);

        self::expectException(UnexpectedTypeException::class);

        $this->promotionRuleTypeValidator->validate($this->promotionRule, $constraint);
    }

    public function testThrowsAnExceptionIfValueIsNotAnInstanceOfPromotionRuleInterface(): void
    {
        self::expectException(UnexpectedValueException::class);

        $this->promotionRuleTypeValidator->validate(new \stdClass(), new PromotionRuleType());
    }

    public function testAddsViolationIfPromotionRuleHasInvalidType(): void
    {
        /** @var ConstraintViolationBuilderInterface&MockObject $constraintViolationBuilder */
        $constraintViolationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $this->promotionRule->expects(self::once())->method('getType')->willReturn('wrong_type');

        $this->context->expects(self::once())
            ->method('buildViolation')
            ->with('sylius.promotion_rule.invalid_type')
            ->willReturn($constraintViolationBuilder);

        $constraintViolationBuilder->expects(self::once())
            ->method('setParameter')
            ->with('{{ available_rule_types }}', 'rule_one, rule_two')
            ->willReturn($constraintViolationBuilder);

        $constraintViolationBuilder->expects(self::once())
            ->method('atPath')->with('type')
            ->willReturn($constraintViolationBuilder);

        $constraintViolationBuilder->expects(self::once())->method('addViolation');

        $this->promotionRuleTypeValidator->validate($this->promotionRule, new PromotionRuleType());
    }
}
