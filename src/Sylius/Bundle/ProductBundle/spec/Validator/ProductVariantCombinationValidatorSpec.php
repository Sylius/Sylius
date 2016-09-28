<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ProductBundle\Validator;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ProductBundle\Validator\Constraint\ProductVariantCombination;
use Sylius\Bundle\ProductBundle\Validator\ProductVariantCombinationValidator;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ProductVariantCombinationValidatorSpec extends ObjectBehavior
{
    function let(ExecutionContextInterface $context)
    {
        $this->initialize($context);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ProductVariantCombinationValidator::class);
    }

    function it_is_a_constraint_validator()
    {
        $this->shouldImplement(ConstraintValidator::class);
    }

    function it_should_not_add_violation_if_variable_dont_have_options(
        ProductVariantInterface $variant,
        ProductInterface $variable
    ) {
        $constraint = new ProductVariantCombination([
            'message' => 'Variant with given options already exists',
        ]);

        $variant->getProduct()->willReturn($variable);

        $variable->hasVariants()->willReturn(false);
        $variable->hasOptions()->willReturn(true);

        $this->validate($variant, $constraint);
    }

    function it_should_not_add_violation_if_variable_dont_have_variants(
        ProductVariantInterface $variant,
        ProductInterface $variable
    ) {
        $constraint = new ProductVariantCombination([
            'message' => 'Variant with given options already exists',
        ]);

        $variant->getProduct()->willReturn($variable);

        $variable->hasVariants()->willReturn(true);
        $variable->hasOptions()->willReturn(false);

        $this->validate($variant, $constraint);
    }

    function it_should_add_violation_if_variant_with_given_same_options_already_exists(
        ProductVariantInterface $variant,
        ProductVariantInterface $existingVariant,
        ProductInterface $variable,
        ProductOptionValueInterface $optionValue,
        $context
    ) {
        $constraint = new ProductVariantCombination([
            'message' => 'Variant with given options already exists',
        ]);

        $variant->getProduct()->willReturn($variable);

        $variant->getOptionValues()->willReturn(new ArrayCollection([$optionValue->getWrappedObject()]));

        $existingVariant->hasOptionValue($optionValue)->willReturn(true);

        $variable->hasVariants()->willReturn(true);
        $variable->hasOptions()->willReturn(true);
        $variable->getVariants()->willReturn([$existingVariant]);

        $context->addViolation('Variant with given options already exists', Argument::any())->shouldBeCalled();

        $this->validate($variant, $constraint);
    }
}
