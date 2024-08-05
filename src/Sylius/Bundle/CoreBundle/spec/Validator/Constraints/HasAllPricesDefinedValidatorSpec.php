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

namespace spec\Sylius\Bundle\CoreBundle\Validator\Constraints;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Validator\Constraints\HasAllPricesDefined;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class HasAllPricesDefinedValidatorSpec extends ObjectBehavior
{
    function let(ExecutionContextInterface $context): void
    {
        $this->initialize($context);
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
            ->during('validate', [new \stdClass(), new HasAllPricesDefined()])
        ;
    }

    function it_throws_an_exception_if_constraint_is_not_a_has_all_prices_defined_constraint(
        ExecutionContextInterface $context,
        Constraint $constraint,
    ): void {
        $context->buildViolation(Argument::any())->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [new \stdClass(), $constraint])
        ;
    }

    function it_does_nothing_if_product_variant_has_no_product(
        ExecutionContextInterface $context,
        ProductVariantInterface $productVariant,
    ): void {
        $context->buildViolation(Argument::any())->shouldNotBeCalled();

        $productVariant->getProduct()->willReturn(null);

        $this->validate($productVariant, new HasAllPricesDefined());
    }

    function it_adds_a_violation_if_product_variant_does_not_have_any_channel_pricing(
        ExecutionContextInterface $context,
        ChannelInterface $firstChannel,
        ChannelInterface $secondChannel,
        ChannelPricingInterface $channelPricing,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
        ProductInterface $product,
        ProductVariantInterface $productVariant,
    ): void {
        $productVariant->getProduct()->willReturn($product);
        $productVariant->getChannelPricingForChannel($firstChannel)->willReturn($channelPricing);
        $productVariant->getChannelPricingForChannel($secondChannel)->willReturn(null);

        $channelPricing->getPrice()->willReturn(1000);

        $product
            ->getChannels()
            ->willReturn(new ArrayCollection([$firstChannel->getWrappedObject(), $secondChannel->getWrappedObject()]))
        ;

        $constraintViolationBuilder->addViolation()->shouldBeCalled();
        $constraintViolationBuilder->atPath(Argument::any())->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->setParameter(Argument::cetera())->willReturn($constraintViolationBuilder);

        $context->buildViolation((new HasAllPricesDefined())->message)->willReturn($constraintViolationBuilder);

        $this->validate($productVariant, new HasAllPricesDefined());
    }

    function it_adds_a_violations_if_product_variant_does_not_have_any_channel_pricing_price_defined(
        ExecutionContextInterface $context,
        ChannelInterface $firstChannel,
        ChannelInterface $secondChannel,
        ChannelPricingInterface $firstChannelPricing,
        ChannelPricingInterface $secondChannelPricing,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
        ProductInterface $product,
        ProductVariantInterface $productVariant,
    ): void {
        $productVariant->getProduct()->willReturn($product);
        $productVariant->getChannelPricingForChannel($firstChannel)->willReturn($firstChannelPricing);
        $productVariant->getChannelPricingForChannel($secondChannel)->willReturn($secondChannelPricing);

        $firstChannelPricing->getPrice()->willReturn(null);
        $secondChannelPricing->getPrice()->willReturn(null);

        $product
            ->getChannels()
            ->willReturn(new ArrayCollection([$firstChannel->getWrappedObject(), $secondChannel->getWrappedObject()]))
        ;

        $constraintViolationBuilder->addViolation()->shouldBeCalledTimes(2);
        $constraintViolationBuilder->atPath(Argument::any())->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->setParameter(Argument::cetera())->willReturn($constraintViolationBuilder);

        $context->buildViolation((new HasAllPricesDefined())->message)->willReturn($constraintViolationBuilder);

        $this->validate($productVariant, new HasAllPricesDefined());
    }

    function it_does_not_add_a_violation_if_product_variant_has_all_channel_pricing_prices_defined(
        ExecutionContextInterface $context,
        ChannelInterface $firstChannel,
        ChannelInterface $secondChannel,
        ChannelPricingInterface $firstChannelPricing,
        ChannelPricingInterface $secondChannelPricing,
        ProductInterface $product,
        ProductVariantInterface $productVariant,
    ): void {
        $productVariant->getProduct()->willReturn($product);
        $productVariant->getChannelPricingForChannel($firstChannel)->willReturn($firstChannelPricing);
        $productVariant->getChannelPricingForChannel($secondChannel)->willReturn($secondChannelPricing);

        $firstChannelPricing->getPrice()->willReturn(1000);
        $secondChannelPricing->getPrice()->willReturn(2000);

        $product
            ->getChannels()
            ->willReturn(new ArrayCollection([$firstChannel->getWrappedObject(), $secondChannel->getWrappedObject()]))
        ;

        $context->buildViolation(Argument::any())->shouldNotBeCalled();

        $this->validate($productVariant, new HasAllPricesDefined());
    }
}
