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
use Sylius\Component\Variation\Model\OptionInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OptionValueCollectionTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('variable_name');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\VariationBundle\Form\Type\OptionValueCollectionType');
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement(FormTypeInterface::class);
    }

    function it_builds_a_form_using_option_name_as_label_if_possible(
        FormBuilderInterface $builder,
        OptionInterface $option
    ) {
        $option->getId()->shouldBeCalled()->willReturn(3);
        $option->getName()->shouldBeCalled()->willReturn('option_name');

        $builder->add('3', 'sylius_variable_name_option_value_choice', [
            'label' => 'option_name',
            'option' => $option,
            'property_path' => '[0]',
        ])->shouldBeCalled();

        $this->buildForm($builder, [
            'options' => [$option],
        ]);
    }

    function it_builds_a_form_using_option_code_as_label_if_name_is_empty(
        FormBuilderInterface $builder,
        OptionInterface $option
    ) {
        $option->getId()->shouldBeCalled()->willReturn(3);
        $option->getName()->shouldBeCalled()->willReturn(null);
        $option->getCode()->shouldBeCalled()->willReturn('option_code');

        $builder->add('3', 'sylius_variable_name_option_value_choice', [
            'label' => 'option_code',
            'option' => $option,
            'property_path' => '[0]',
        ])->shouldBeCalled();

        $this->buildForm($builder, [
            'options' => [$option],
        ]);
    }

    function it_has_options(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'options' => null,
        ])->shouldBeCalled();

        $this->configureOptions($resolver);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_variable_name_option_value_collection');
    }
}
