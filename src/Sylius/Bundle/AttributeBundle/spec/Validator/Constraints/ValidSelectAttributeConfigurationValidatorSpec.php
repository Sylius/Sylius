<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\AttributeBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\AttributeBundle\Validator\Constraints\ValidSelectAttributeConfiguration;
use Sylius\Bundle\AttributeBundle\Validator\Constraints\ValidTextAttributeConfiguration;
use Sylius\Component\Attribute\AttributeType\SelectAttributeType;
use Sylius\Component\Attribute\AttributeType\TextAttributeType;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ValidSelectAttributeConfigurationValidatorSpec extends ObjectBehavior
{
    function let(ExecutionContextInterface $context): void
    {
        $this->initialize($context);
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldHaveType(ConstraintValidator::class);
    }

    function it_adds_a_violation_if_max_entries_value_is_lower_than_min_entries_value(
        ExecutionContextInterface $context,
        AttributeInterface $attribute
    ): void {
        $constraint = new ValidSelectAttributeConfiguration();

        $attribute->getType()->willReturn(SelectAttributeType::TYPE);
        $attribute->getConfiguration()->willReturn(['multiple' => true, 'min' => 6, 'max' => 4]);

        $context->addViolation(Argument::any())->shouldBeCalled();

        $this->validate($attribute, $constraint);
    }

    function it_adds_a_violation_if_min_entries_value_is_greater_than_the_number_of_added_choices(
        ExecutionContextInterface $context,
        AttributeInterface $attribute
    ): void {
        $constraint = new ValidSelectAttributeConfiguration();

        $attribute->getType()->willReturn(SelectAttributeType::TYPE);
        $attribute->getConfiguration()->willReturn([
            'multiple' => true,
            'min' => 4,
            'max' => 6,
            'choices' => [
                'ec134e10-6a80-4eaf-8346-e9bb0f7406a4' => 'Banana',
                '63148775-be39-47eb-8afd-a4818981e3c0' => 'Watermelon',
            ],
        ]);

        $context->addViolation(Argument::any())->shouldBeCalled();

        $this->validate($attribute, $constraint);
    }

    function it_adds_a_violation_if_multiple_is_not_true_when_min_or_max_entries_values_are_specified(
        ExecutionContextInterface $context,
        AttributeInterface $attribute
    ): void {
        $constraint = new ValidSelectAttributeConfiguration();

        $attribute->getType()->willReturn(SelectAttributeType::TYPE);
        $attribute->getConfiguration()->willReturn(['multiple' => false, 'min' => 4, 'max' => 6]);

        $context->addViolation(Argument::any())->shouldBeCalled();

        $this->validate($attribute, $constraint);
    }

    function it_does_nothing_if_an_attribute_is_not_a_select_type(
        ExecutionContextInterface $context,
        AttributeInterface $attribute
    ): void {
        $constraint = new ValidSelectAttributeConfiguration();

        $attribute->getType()->willReturn(TextAttributeType::TYPE);

        $context->addViolation(Argument::any())->shouldNotBeCalled();

        $this->validate($attribute, $constraint);
    }

    function it_throws_an_exception_if_validated_value_is_not_an_attribute(): void
    {
        $constraint = new ValidSelectAttributeConfiguration();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', ['badObject', $constraint])
        ;
    }

    function it_throws_an_exception_if_constraint_is_not_a_valid_select_attribute_configuration_constraint(
        AttributeInterface $attribute
    ): void {
        $constraint = new ValidTextAttributeConfiguration();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [$attribute, $constraint])
        ;
    }
}
