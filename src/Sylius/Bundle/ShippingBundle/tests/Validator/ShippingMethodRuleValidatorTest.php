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

namespace Tests\Sylius\Bundle\ShippingBundle\Validator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ShippingBundle\Validator\Constraint\ShippingMethodRule;
use Sylius\Bundle\ShippingBundle\Validator\ShippingMethodRuleValidator;
use Sylius\Component\Shipping\Model\ShippingMethodRuleInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Validator\ContextualValidatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class ShippingMethodRuleValidatorTest extends TestCase
{
    private ExecutionContextInterface&MockObject $executionContext;

    private ShippingMethodRuleValidator $shippingMethodRuleValidator;

    protected function setUp(): void
    {
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->shippingMethodRuleValidator = new ShippingMethodRuleValidator([
            'total_weight_greater_than_or_equal' => 'sylius.form.shipping_method_rule.total_weight_greater_than_or_equal',
            'order_total_greater_than_or_equal' => 'sylius.form.shipping_method_rule.items_total_greater_than_or_equal',
            'different_rule' => 'sylius.form.shipping_method_rule.different_rule',
        ], [
            'total_weight_greater_than_or_equal' => ['sylius', 'total_weight'],
            'order_total_greater_than_or_equal' => ['sylius', 'order_total'],
        ]);
        $this->shippingMethodRuleValidator->initialize($this->executionContext);
    }

    public function testThrowsAnExceptionIfConstraintIsNotAnInstanceOfShippingMethodRule(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        /** @var Constraint&MockObject $constraint */
        $constraint = $this->createMock(Constraint::class);
        /** @var ShippingMethodRuleInterface&MockObject $shippingMethodRule */
        $shippingMethodRule = $this->createMock(ShippingMethodRuleInterface::class);

        $this->shippingMethodRuleValidator->validate($shippingMethodRule, $constraint);
    }

    public function testAddsViolationToShippingMethodRuleWithWrongType(): void
    {
        /** @var ConstraintViolationBuilderInterface&MockObject $constraintViolationBuilder */
        $constraintViolationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        /** @var ShippingMethodRuleInterface&MockObject $shippingMethodRule */
        $shippingMethodRule = $this->createMock(ShippingMethodRuleInterface::class);

        $shippingMethodRule->expects($this->once())->method('getType')->willReturn('wrong_rule');
        $this->executionContext->expects($this->once())->method('buildViolation')->with((new ShippingMethodRule())->invalidType)->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->expects($this->once())->method('setParameter')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->expects($this->once())->method('atPath')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->expects($this->once())->method('addViolation');

        $this->shippingMethodRuleValidator->validate($shippingMethodRule, new ShippingMethodRule());
    }

    public function testCallsAValidatorWithGroup(): void
    {
        /** @var ShippingMethodRuleInterface&MockObject $shippingMethodRule */
        $shippingMethodRule = $this->createMock(ShippingMethodRuleInterface::class);
        /** @var ValidatorInterface&MockObject $validator */
        $validator = $this->createMock(ValidatorInterface::class);
        /** @var ContextualValidatorInterface&MockObject $contextualValidator */
        $contextualValidator = $this->createMock(ContextualValidatorInterface::class);

        $shippingMethodRule->expects($this->once())->method('getType')->willReturn('total_weight_greater_than_or_equal');
        $this->executionContext->expects($this->once())->method('getValidator')->willReturn($validator);
        $validator->expects($this->once())->method('inContext')->with($this->executionContext)->willReturn($contextualValidator);
        $contextualValidator->expects($this->once())->method('validate')->with($shippingMethodRule, null, ['sylius', 'total_weight'])->willReturn($contextualValidator);

        $this->shippingMethodRuleValidator->validate($shippingMethodRule, new ShippingMethodRule(['groups' => ['sylius', 'total_weight']]));
    }

    public function testCallsValidatorWithDefaultGroupsIfNoneAssignedToShippingMethodRule(): void
    {
        /** @var ShippingMethodRuleInterface&MockObject $shippingMethodRule */
        $shippingMethodRule = $this->createMock(ShippingMethodRuleInterface::class);
        /** @var ValidatorInterface&MockObject $validator */
        $validator = $this->createMock(ValidatorInterface::class);
        /** @var ContextualValidatorInterface&MockObject $contextualValidator */
        $contextualValidator = $this->createMock(ContextualValidatorInterface::class);

        $shippingMethodRule->expects($this->once())->method('getType')->willReturn('different_rule');
        $this->executionContext->expects($this->once())->method('getValidator')->willReturn($validator);
        $validator->expects($this->once())->method('inContext')->with($this->executionContext)->willReturn($contextualValidator);
        $contextualValidator->expects($this->once())->method('validate')->with($shippingMethodRule, null, ['sylius'])->willReturn($contextualValidator);

        $this->shippingMethodRuleValidator->validate($shippingMethodRule, new ShippingMethodRule(['groups' => ['sylius']]));
    }
}
