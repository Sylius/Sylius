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

use PhpSpec\ObjectBehavior;
use Sylius\Component\Variation\Model\OptionValueInterface;
use Sylius\Component\Variation\Model\VariableInterface;
use Sylius\Component\Variation\Model\VariantInterface;

class VariantToCombinationTransformerSpec extends ObjectBehavior
{
    public function let(VariableInterface $variable)
    {
        $this->beConstructedWith($variable);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\VariationBundle\Form\DataTransformer\VariantToCombinationTransformer');
    }

    public function it_is_a_form_data_transformer()
    {
        $this->shouldImplement('Symfony\Component\Form\DataTransformerInterface');
    }

    public function it_should_transform_null_into_array()
    {
        $this->transform(null)->shouldReturn(array());
    }

    public function it_should_not_transform_not_supported_data_and_throw_exception()
    {
        $this->shouldThrow('Symfony\Component\Form\Exception\UnexpectedTypeException')->duringTransform(array());
    }

    public function it_should_transform_variant_into_variant_options(VariantInterface $variant)
    {
        $variant->getOptions()->willReturn(array());

        $this->transform($variant)->shouldReturn(array());
    }

    public function it_should_reverse_transform_null_into_null()
    {
        $this->reverseTransform(null)->shouldReturn(null);
    }

    public function it_should_reverse_transform_empty_string_into_null()
    {
        $this->reverseTransform('')->shouldReturn(null);
    }

    public function it_should_not_reverse_transform_not_supported_data_and_throw_exception()
    {
        $this->shouldThrow('Symfony\Component\Form\Exception\UnexpectedTypeException')
            ->duringReverseTransform(new \stdClass());
    }

    public function it_should_reverse_transform_variable_without_variants_into_null(
        VariableInterface $variable,
        OptionValueInterface $optionValue
    ) {
        $variable->getVariants()->willReturn(array());

        $this->reverseTransform(array($optionValue))->shouldReturn(null);
    }

    public function it_should_reverse_transform_variable_with_variants_if_options_match(
        VariableInterface $variable,
        VariantInterface $variant,
        OptionValueInterface $optionValue
    ) {
        $variable->getVariants()->willReturn(array($variant));

        $variant->hasOption($optionValue)->willReturn(true);

        $this->reverseTransform(array($optionValue))->shouldReturn($variant);
    }

    public function it_should_not_reverse_transform_variable_with_variants_if_options_not_match(
        VariableInterface $variable,
        VariantInterface $variant,
        OptionValueInterface $optionValue
    ) {
        $variable->getVariants()->willReturn(array($variant));

        $variant->hasOption($optionValue)->willReturn(false);

        $this->reverseTransform(array($optionValue))->shouldReturn(null);
    }

    public function it_should_not_reverse_transform_variable_with_variants_if_options_are_missing(
        VariableInterface $variable,
        VariantInterface $variant
    ) {
        $variable->getVariants()->willReturn(array($variant));

        $this->reverseTransform(array(null))->shouldReturn(null);
    }
}
