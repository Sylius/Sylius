<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\VariationBundle\Validator;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\VariationBundle\Validator\Constraint\VariantCombination;
use Sylius\Component\Variation\Model\OptionValueInterface;
use Sylius\Component\Variation\Model\VariableInterface;
use Sylius\Component\Variation\Model\VariantInterface;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class VariantCombinationValidatorSpec extends ObjectBehavior
{
    function let(ExecutionContextInterface $context)
    {
        $this->initialize($context);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\VariationBundle\Validator\VariantCombinationValidator');
    }

    function it_is_a_constraint_validator()
    {
        $this->shouldImplement(ConstraintValidator::class);
    }

    function it_should_not_add_violation_if_variable_dont_have_options(
        VariantInterface $variant,
        VariableInterface $variable
    ) {
        $constraint = new VariantCombination([
            'message' => 'Variant with given options already exists',
        ]);

        $variant->getObject()->willReturn($variable);

        $variable->hasVariants()->willReturn(false);
        $variable->hasOptions()->willReturn(true);

        $this->validate($variant, $constraint);
    }

    function it_should_not_add_violation_if_variable_dont_have_variants(
        VariantInterface $variant,
        VariableInterface $variable
    ) {
        $constraint = new VariantCombination([
            'message' => 'Variant with given options already exists',
        ]);

        $variant->getObject()->willReturn($variable);

        $variable->hasVariants()->willReturn(true);
        $variable->hasOptions()->willReturn(false);

        $this->validate($variant, $constraint);
    }

    function it_should_add_violation_if_variant_with_given_same_options_already_exists(
        VariantInterface $variant,
        VariantInterface $existingVariant,
        VariableInterface $variable,
        OptionValueInterface $option,
        $context
    ) {
        $constraint = new VariantCombination([
            'message' => 'Variant with given options already exists',
        ]);

        $variant->getObject()->willReturn($variable);

        $variant->getOptions()->willReturn(new ArrayCollection([$option->getWrappedObject()]));

        $existingVariant->hasOption($option)->willReturn(true);

        $variable->hasVariants()->willReturn(true);
        $variable->hasOptions()->willReturn(true);
        $variable->getVariants()->willReturn([$existingVariant]);

        $context->addViolation('Variant with given options already exists', Argument::any())->shouldBeCalled();

        $this->validate($variant, $constraint);
    }
}
