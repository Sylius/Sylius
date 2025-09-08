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
use Sylius\Bundle\PromotionBundle\Validator\Constraints\PromotionRuleGroup;
use Sylius\Bundle\PromotionBundle\Validator\PromotionRuleGroupValidator;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Validator\ContextualValidatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PromotionRuleGroupValidatorTest extends TestCase
{
    private PromotionRuleGroupValidator $promotionRuleGroupValidator;

    private ExecutionContextInterface&MockObject $context;

    private MockObject&ValidatorInterface $validator;

    private ContextualValidatorInterface&MockObject $contextualValidator;

    private MockObject&PromotionRuleInterface $promotionRule;

    protected function setUp(): void
    {
        parent::setUp();
        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->promotionRuleGroupValidator = new PromotionRuleGroupValidator(['rule_two' => ['Default', 'rule_two']]);
        $this->promotionRuleGroupValidator->initialize($this->context);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->contextualValidator = $this->createMock(ContextualValidatorInterface::class);
        $this->promotionRule = $this->createMock(PromotionRuleInterface::class);
    }

    public function testThrowsExceptionIfConstraintIsNotPromotionRuleGroup(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $promotionRule = $this->createMock(PromotionRuleInterface::class);
        $constraint = $this->createMock(Constraint::class);

        $this->promotionRuleGroupValidator->validate($promotionRule, $constraint);
    }

    public function testThrowsExceptionIfValueIsNotPromotionRule(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $constraint = new PromotionRuleGroup();

        $this->promotionRuleGroupValidator->validate(new \stdClass(), $constraint);
    }

    public function testCallsValidatorWithGroup(): void
    {
        $this->promotionRule->method('getType')->willReturn('rule_two');

        $this->context->method('getValidator')->willReturn($this->validator);
        $this->validator->method('inContext')->with($this->context)->willReturn($this->contextualValidator);

        $this->contextualValidator
            ->expects($this->once())
            ->method('validate')
            ->with($this->promotionRule, null, ['Default', 'rule_two'])
            ->willReturn($this->contextualValidator);

        $constraint = new PromotionRuleGroup(['groups' => ['Default', 'test_group']]);
        $this->promotionRuleGroupValidator->validate($this->promotionRule, $constraint);
    }

    public function testCallsValidatorWithDefaultGroupsIfNoneProvided(): void
    {
        $this->promotionRule->method('getType')->willReturn('rule_one');

        $this->context->method('getValidator')->willReturn($this->validator);
        $this->validator->method('inContext')->with($this->context)->willReturn($this->contextualValidator);

        $this->contextualValidator
            ->expects($this->once())
            ->method('validate')
            ->with($this->promotionRule, null, ['Default', 'test_group'])
            ->willReturn($this->contextualValidator);

        $constraint = new PromotionRuleGroup(['groups' => ['Default', 'test_group']]);
        $this->promotionRuleGroupValidator->validate($this->promotionRule, $constraint);
    }
}
