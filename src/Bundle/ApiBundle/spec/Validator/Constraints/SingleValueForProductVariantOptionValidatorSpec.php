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

namespace spec\Sylius\Bundle\ApiBundle\Validator\Constraints;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Validator\Constraints\SingleValueForProductVariantOption;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class SingleValueForProductVariantOptionValidatorSpec extends ObjectBehavior
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
            ->during('validate', [new \stdClass(), new SingleValueForProductVariantOption()])
        ;
    }

    function it_throws_an_exception_if_constraint_is_not_a_single_value_for_product_variant_option(
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

    function it_adds_violation_if_there_is_more_than_one_option_value_to_a_single_option(
        ExecutionContextInterface $executionContext,
        ProductVariantInterface $variant,
        ProductOptionValueInterface $firstProductOptionValue,
        ProductOptionValueInterface $secondProductOptionValue,
    ): void {
        $constraint = new SingleValueForProductVariantOption();

        $firstProductOptionValue->getOptionCode()->willReturn('OPTION');
        $secondProductOptionValue->getOptionCode()->willReturn('OPTION');

        $variant->getOptionValues()->willReturn(new ArrayCollection([
            $firstProductOptionValue->getWrappedObject(),
            $secondProductOptionValue->getWrappedObject(),
        ]));

        $executionContext->addViolation('sylius.product_variant.option_values.single_value')->shouldBeCalled();

        $this->validate($variant, $constraint);
    }

    function it_does_nothing_if_each_option_has_only_one_option_value(
        ExecutionContextInterface $executionContext,
        ProductVariantInterface $variant,
        ProductOptionValueInterface $firstProductOptionValue,
        ProductOptionValueInterface $secondProductOptionValue,
    ): void {
        $constraint = new SingleValueForProductVariantOption();

        $firstProductOptionValue->getOptionCode()->willReturn('OPTION');
        $secondProductOptionValue->getOptionCode()->willReturn('DIFFERENT_OPTION');

        $variant->getOptionValues()->willReturn(new ArrayCollection([
            $firstProductOptionValue->getWrappedObject(),
            $secondProductOptionValue->getWrappedObject(),
        ]));

        $executionContext->addViolation($constraint->message)->shouldNotBeCalled();

        $this->validate($variant, $constraint);
    }
}
