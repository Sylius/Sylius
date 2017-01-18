<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ProductBundle\Form\DataTransformer;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ProductBundle\Form\DataTransformer\ProductVariantToProductOptionsTransformer;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class ProductVariantToProductOptionsTransformerSpec extends ObjectBehavior
{
    function let(ProductInterface $variable)
    {
        $this->beConstructedWith($variable);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ProductVariantToProductOptionsTransformer::class);
    }

    function it_is_a_data_transformer()
    {
        $this->shouldImplement(DataTransformerInterface::class);
    }

    function it_transforms_null_to_array()
    {
        $this->transform(null)->shouldReturn([]);
    }

    function it_does_not_transform_not_supported_data_and_throw_exception()
    {
        $this->shouldThrow(UnexpectedTypeException::class)->duringTransform([]);
    }

    function it_transforms_variant_into_variant_options(ProductVariantInterface $variant, Collection $optionValues)
    {
        $variant->getOptionValues()->willReturn($optionValues);
        $optionValues->toArray()->willReturn([]);

        $this->transform($variant)->shouldReturn([]);
    }

    function it_reverse_transforms_null_into_null()
    {
        $this->reverseTransform(null)->shouldReturn(null);
    }

    function it_reverse_transforms_empty_string_into_null()
    {
        $this->reverseTransform('')->shouldReturn(null);
    }

    function it_does_not_reverse_transform_not_supported_data_and_throw_exception()
    {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->duringReverseTransform(new \stdClass())
        ;
    }

    function it_throws_exception_when_trying_to_reverse_transform_variable_without_variants(
        ProductInterface $variable,
        ProductOptionValueInterface $optionValue
    ) {
        $variable->getVariants()->willReturn([]);
        $variable->getCode()->willReturn('example');

        $this
            ->shouldThrow(TransformationFailedException::class)
            ->duringReverseTransform([$optionValue]);
    }

    function it_reverse_transforms_variable_with_variants_if_options_match(
        ProductInterface $variable,
        ProductVariantInterface $variant,
        ProductOptionValueInterface $optionValue
    ) {
        $variable->getVariants()->willReturn([$variant]);

        $variant->hasOptionValue($optionValue)->willReturn(true);

        $this->reverseTransform([$optionValue])->shouldReturn($variant);
    }

    function it_throws_exception_when_trying_to_reverse_transform_variable_with_variants_if_options_not_match(
        ProductInterface $variable,
        ProductVariantInterface $variant,
        ProductOptionValueInterface $optionValue
    ) {
        $variable->getVariants()->willReturn([$variant]);
        $variable->getCode()->willReturn('example');

        $variant->hasOptionValue($optionValue)->willReturn(false);

        $this
            ->shouldThrow(TransformationFailedException::class)
            ->duringReverseTransform([$optionValue]);
    }

    function it_throws_exception_when_trying_to_reverse_transform_variable_with_variants_if_options_are_missing(
        ProductInterface $variable,
        ProductVariantInterface $variant
    ) {
        $variable->getVariants()->willReturn([$variant]);
        $variable->getCode()->willReturn('example');

        $this
            ->shouldThrow(TransformationFailedException::class)
            ->duringReverseTransform([null]);
    }
}
