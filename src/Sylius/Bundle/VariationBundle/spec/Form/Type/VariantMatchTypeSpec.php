<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\VariationBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\VariationBundle\Form\DataTransformer\VariantToCombinationTransformer;
use Sylius\Component\Variation\Model\OptionInterface;
use Sylius\Component\Variation\Model\VariableInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\Test\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VariantMatchTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('variable_name');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\VariationBundle\Form\Type\VariantMatchType');
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement(FormTypeInterface::class);
    }

    function it_builds_a_form(FormBuilderInterface $builder, VariableInterface $variable, OptionInterface $option)
    {
        $variable->getOptions()->shouldBeCalled()->willReturn([$option]);
        $option->getName()->shouldBeCalled()->willReturn('option_name');
        $option->getCode()->shouldBeCalled()->willReturn('option-name-code');

        $builder->add('option-name-code', 'sylius_variable_name_option_value_choice', [
            'label' => 'option_name',
            'option' => $option,
            'property_path' => '[0]',
        ])->shouldBeCalled();

        $builder->addModelTransformer(
            Argument::type(VariantToCombinationTransformer::class)
        )->shouldBeCalled();

        $this->buildForm($builder, ['variable' => $variable]);
    }

    function it_has_options(OptionsResolver $resolver)
    {
        $resolver->setRequired([
            'variable',
        ])->shouldBeCalled()->willReturn($resolver);

        $resolver->setAllowedTypes('variable', VariableInterface::class)->shouldBeCalled()->willReturn($resolver);

        $this->configureOptions($resolver);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_variable_name_variant_match');
    }
}
