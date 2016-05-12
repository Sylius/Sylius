<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\VariationBundle\Form\DataTransformer;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Variation\Model\OptionValueInterface;
use Sylius\Component\Variation\Model\VariableInterface;
use Sylius\Component\Variation\Model\VariantInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

class VariantToCombinationTransformerSpec extends ObjectBehavior
{
    function let(VariableInterface $variable)
    {
        $this->beConstructedWith($variable);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\VariationBundle\Form\DataTransformer\VariantToCombinationTransformer');
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

    function it_should_transform_variant_into_variant_options(VariantInterface $variant, Collection $options)
    {
        $variant->getOptions()->willReturn($options);
        $options->toArray()->willReturn([]);

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
        VariableInterface $variable,
        OptionValueInterface $optionValue
    ) {
        $variable->getVariants()->willReturn([]);

        $this->reverseTransform([$optionValue])->shouldReturn(null);
    }

    function it_should_reverse_transform_variable_with_variants_if_options_match(
        VariableInterface $variable,
        VariantInterface $variant,
        OptionValueInterface $optionValue
    ) {
        $variable->getVariants()->willReturn([$variant]);

        $variant->hasOption($optionValue)->willReturn(true);

        $this->reverseTransform([$optionValue])->shouldReturn($variant);
    }

    function it_should_not_reverse_transform_variable_with_variants_if_options_not_match(
        VariableInterface $variable,
        VariantInterface $variant,
        OptionValueInterface $optionValue
    ) {
        $variable->getVariants()->willReturn([$variant]);

        $variant->hasOption($optionValue)->willReturn(false);

        $this->reverseTransform([$optionValue])->shouldReturn(null);
    }

    function it_should_not_reverse_transform_variable_with_variants_if_options_are_missing(
        VariableInterface $variable,
        VariantInterface $variant
    ) {
        $variable->getVariants()->willReturn([$variant]);

        $this->reverseTransform([null])->shouldReturn(null);
    }
}
