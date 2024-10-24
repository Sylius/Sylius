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

namespace spec\Sylius\Bundle\AttributeBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\AttributeBundle\Validator\Constraints\AttributeType;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class AttributeTypeValidatorSpec extends ObjectBehavior
{
    function let(ServiceRegistryInterface $attributeTypesRegistry, ExecutionContextInterface $context): void
    {
        $this->beConstructedWith($attributeTypesRegistry);
        $this->initialize($context);
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldHaveType(ConstraintValidator::class);
    }

    function it_throws_exception_when_value_is_not_an_attribute(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [new \stdClass(), new AttributeType()])
        ;
    }

    function it_throws_exception_when_constraint_is_not_an_attribute_type(
        AttributeInterface $attribute,
        Constraint $constraint,
    ): void {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [$attribute, $constraint]);
    }

    function it_does_nothing_when_attribute_type_is_null(
        ServiceRegistryInterface $attributeTypesRegistry,
        ExecutionContextInterface $context,
        AttributeInterface $attribute,
    ): void {
        $attribute->getType()->willReturn(null);

        $attributeTypesRegistry->has(Argument::any())->shouldNotBeCalled();
        $context->addViolation(Argument::any())->shouldNotBeCalled();

        $this->validate($attribute, new AttributeType());
    }

    function it_does_nothing_when_attribute_type_is_registered(
        ServiceRegistryInterface $attributeTypesRegistry,
        ExecutionContextInterface $context,
        AttributeInterface $attribute,
    ): void {
        $attribute->getType()->willReturn('foo');

        $attributeTypesRegistry->has('foo')->willReturn(true);

        $context->addViolation(Argument::any())->shouldNotBeCalled();

        $this->validate($attribute, new AttributeType());
    }

    function it_adds_violation_when_attribute_type_is_not_registered(
        ServiceRegistryInterface $attributeTypesRegistry,
        ExecutionContextInterface $context,
        ConstraintViolationBuilderInterface $violationBuilder,
        AttributeInterface $attribute,
    ): void {
        $constraint = new AttributeType();

        $attribute->getType()->willReturn('foo');
        $attributeTypesRegistry->has('foo')->willReturn(false);
        $attributeTypesRegistry->all()->willReturn(['foo_attribute_name' => 'foo_value', 'bar_attribute_name' => 'bar_value']);

        $context
            ->buildViolation($constraint->unregisteredAttributeTypeMessage, [
                '%type%' => 'foo', '%available_types%' => 'foo_attribute_name, bar_attribute_name',
            ])
            ->shouldBeCalled()
            ->willReturn($violationBuilder);
        $violationBuilder->atPath('type')->shouldBeCalled()->willReturn($violationBuilder);
        $violationBuilder->addViolation()->shouldBeCalled();

        $this->validate($attribute, $constraint);
    }
}
