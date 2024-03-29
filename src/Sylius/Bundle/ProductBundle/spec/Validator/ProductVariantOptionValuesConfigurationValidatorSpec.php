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

namespace spec\Sylius\Bundle\ProductBundle\Validator;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ProductBundle\Validator\Constraint\ProductVariantOptionValuesConfiguration;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ProductVariantOptionValuesConfigurationValidatorSpec extends ObjectBehavior
{
    function let(ExecutionContextInterface $executionContext): void
    {
        $this->initialize($executionContext);
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldImplement(ConstraintValidatorInterface::class);
    }

    function it_throws_an_exception_if_value_is_not_a_product_variant(
        ExecutionContextInterface $context,
    ): void {
        $context->buildViolation(Argument::any())->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [new \stdClass(), new ProductVariantOptionValuesConfiguration()])
        ;
    }

    function it_throws_an_exception_if_constraint_is_not_a_product_variant_option_values_configuration(
        ExecutionContextInterface $context,
        ProductVariantInterface $variant,
        Constraint $constraint,
    ): void {
        $context->buildViolation(Argument::any())->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [$variant, $constraint])
        ;
    }

    function it_adds_violation_if_not_all_options_have_configured_values_on_the_variant(
        ExecutionContextInterface $executionContext,
        ProductVariantInterface $variant,
        ProductInterface $product,
        ProductOptionInterface $firstOption,
        ProductOptionInterface $secondOption,
        ProductOptionValueInterface $optionValue,
    ): void {
        $constraint = new ProductVariantOptionValuesConfiguration();

        $variant->getProduct()->willReturn($product);
        $product->hasOptions()->willReturn(true);

        $firstOption->getCode()->willReturn('SIZE');
        $secondOption->getCode()->willReturn('COLOUR');
        $product->getOptions()->willReturn(new ArrayCollection([
            $firstOption->getWrappedObject(),
            $secondOption->getWrappedObject(),
        ]));

        $optionValue->getOptionCode()->willReturn('SIZE');
        $variant->getOptionValues()->willReturn(new ArrayCollection([$optionValue->getWrappedObject()]));

        $executionContext->addViolation($constraint->message)->shouldBeCalled();

        $this->validate($variant, $constraint);
    }

    function it_does_nothing_if_all_options_have_configured_values_on_the_variant(
        ExecutionContextInterface $executionContext,
        ProductVariantInterface $variant,
        ProductInterface $product,
        ProductOptionInterface $firstOption,
        ProductOptionInterface $secondOption,
        ProductOptionValueInterface $firstProductOptionValue,
        ProductOptionValueInterface $secondProductOptionValue,
    ): void {
        $constraint = new ProductVariantOptionValuesConfiguration();

        $variant->getProduct()->willReturn($product);
        $product->hasOptions()->willReturn(true);

        $firstOption->getCode()->willReturn('SIZE');
        $secondOption->getCode()->willReturn('COLOUR');
        $product->getOptions()->willReturn(new ArrayCollection([
            $firstOption->getWrappedObject(),
            $secondOption->getWrappedObject(),
        ]));

        $firstProductOptionValue->getOptionCode()->willReturn('SIZE');
        $secondProductOptionValue->getOptionCode()->willReturn('COLOUR');
        $variant->getOptionValues()->willReturn(new ArrayCollection([
            $firstProductOptionValue->getWrappedObject(),
            $secondProductOptionValue->getWrappedObject(),
        ]));

        $executionContext->addViolation($constraint->message)->shouldNotBeCalled();

        $this->validate($variant, $constraint);
    }

    function it_does_nothing_if_variant_does_not_have_product(
        ExecutionContextInterface $executionContext,
        ProductVariantInterface $variant,
    ): void {
        $constraint = new ProductVariantOptionValuesConfiguration();

        $variant->getProduct()->willReturn(null);

        $executionContext->addViolation($constraint->message)->shouldNotBeCalled();

        $this->validate($variant, $constraint);
    }

    function it_does_nothing_if_product_does_not_have_options(
        ExecutionContextInterface $executionContext,
        ProductVariantInterface $variant,
        ProductInterface $product,
    ): void {
        $constraint = new ProductVariantOptionValuesConfiguration();

        $variant->getProduct()->willReturn($product);
        $product->hasOptions()->willReturn(false);

        $executionContext->addViolation($constraint->message)->shouldNotBeCalled();

        $this->validate($variant, $constraint);
    }
}
