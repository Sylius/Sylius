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

namespace spec\Sylius\Bundle\CoreBundle\Validator\Constraints;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Validator\Constraints\HasAllOriginalPricesDefined;
use Sylius\Bundle\CoreBundle\Validator\Constraints\HasAllPricesDefined;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class HasAllOriginalPricesDefinedValidatorSpec extends ObjectBehavior
{
    function let(ExecutionContext $executionContext): void
    {
        $this->initialize($executionContext);
    }

    function it_throws_exception_if_constraint_is_not_an_instance_of_has_all_original_prices_defined(
        ProductVariantInterface $productVariant
    ): void {
        $this->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [
                $productVariant,
                new HasAllPricesDefined()
            ])
        ;
    }

    function it_adds_violation_if_all_channel_pricings_are_not_defined(
        ProductVariantInterface $productVariant,
        ProductInterface $product,
        ChannelInterface $firstChannel,
        ChannelInterface $secondChannel,
        ChannelPricingInterface $firstChannelPricing,
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder
    ): void {
        $productVariant->getProduct()->willReturn($product);
        $product->getChannels()->willReturn(
            new ArrayCollection([$firstChannel->getWrappedObject(), $secondChannel->getWrappedObject()])
        );

        $productVariant->getChannelPricingForChannel($firstChannel)->willReturn($firstChannelPricing);
        $firstChannelPricing->getOriginalPrice()->willReturn(3000);

        $productVariant->getChannelPricingForChannel($secondChannel)->willReturn(null);

        $executionContext->buildViolation('sylius.product_variant.channel_pricing.all_original_prices_defined')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath('channelPricings')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $this->validate($productVariant, new HasAllOriginalPricesDefined());
    }

    function it_adds_violation_if_all_original_prices_are_not_declared(
        ProductVariantInterface $productVariant,
        ProductInterface $product,
        ChannelInterface $firstChannel,
        ChannelInterface $secondChannel,
        ChannelPricingInterface $firstChannelPricing,
        ChannelPricingInterface $secondChannelPricing,
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder
    ): void {
        $productVariant->getProduct()->willReturn($product);
        $product->getChannels()->willReturn(
            new ArrayCollection([$firstChannel->getWrappedObject(), $secondChannel->getWrappedObject()])
        );

        $productVariant->getChannelPricingForChannel($firstChannel)->willReturn($firstChannelPricing);
        $firstChannelPricing->getOriginalPrice()->willReturn(3000);

        $productVariant->getChannelPricingForChannel($secondChannel)->willReturn($secondChannelPricing);
        $secondChannelPricing->getOriginalPrice()->willReturn(null);

        $executionContext->buildViolation('sylius.product_variant.channel_pricing.all_original_prices_defined')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath('channelPricings')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $this->validate($productVariant, new HasAllOriginalPricesDefined());
    }
}
