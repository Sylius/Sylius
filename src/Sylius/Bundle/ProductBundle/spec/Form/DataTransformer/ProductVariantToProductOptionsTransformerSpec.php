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

    function it_is_a_form_data_transformer()
    {
        $this->shouldImplement(DataTransformerInterface::class);
    }

    function it_should_transform_null_into_array()
    {
        $this->transform(null)->shouldReturn([]);
    }

    function it_should_not_transform_not_supported_data_and_throw_exception()
    {
        $this->shouldThrow(UnexpectedTypeException::class)->duringTransform([]);
    }

    function it_should_transform_variant_into_variant_options(ProductVariantInterface $variant, Collection $optionValues)
    {
        $variant->getOptionValues()->willReturn($optionValues);
        $optionValues->toArray()->willReturn([]);

        $this->transform($variant)->shouldReturn([]);
    }

    function it_should_reverse_transform_null_into_null()
    {
        $this->reverseTransform(null)->shouldReturn(null);
    }

    function it_should_reverse_transform_empty_string_into_null()
    {
        $this->reverseTransform('')->shouldReturn(null);
    }

    function it_should_not_reverse_transform_not_supported_data_and_throw_exception()
    {
        $this->shouldThrow(UnexpectedTypeException::class)
            ->duringReverseTransform(new \stdClass());
    }

    function it_should_reverse_transform_variable_without_variants_into_null(
        ProductInterface $variable,
        ProductOptionValueInterface $optionValue
    ) {
        $variable->getVariants()->willReturn([]);

        $this->reverseTransform([$optionValue])->shouldReturn(null);
    }

    function it_should_reverse_transform_variable_with_variants_if_options_match(
        ProductInterface $variable,
        ProductVariantInterface $variant,
        ProductOptionValueInterface $optionValue
    ) {
        $variable->getVariants()->willReturn([$variant]);

        $variant->hasOptionValue($optionValue)->willReturn(true);

        $this->reverseTransform([$optionValue])->shouldReturn($variant);
    }

    function it_should_not_reverse_transform_variable_with_variants_if_options_not_match(
        ProductInterface $variable,
        ProductVariantInterface $variant,
        ProductOptionValueInterface $optionValue
    ) {
        $variable->getVariants()->willReturn([$variant]);

        $variant->hasOptionValue($optionValue)->willReturn(false);

        $this->reverseTransform([$optionValue])->shouldReturn(null);
    }

    function it_should_not_reverse_transform_variable_with_variants_if_options_are_missing(
        ProductInterface $variable,
        ProductVariantInterface $variant
    ) {
        $variable->getVariants()->willReturn([$variant]);

        $this->reverseTransform([null])->shouldReturn(null);
    }
}
