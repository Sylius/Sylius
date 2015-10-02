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
use Sylius\Component\Product\Model\OptionInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OptionValueCollectionTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('varibale_name');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\VariationBundle\Form\Type\OptionValueCollectionType');
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    function it_builds_a_form_using_option_presenation_as_label_if_possible(
        FormBuilderInterface $builder,
        OptionInterface $option
    ) {
        $option->getId()->shouldBeCalled()->willReturn(3);
        $option->getPresentation()->shouldBeCalled()->willReturn(null);
        $option->getName()->shouldBeCalled()->willReturn('option_name');

        $builder->add('3', 'sylius_varibale_name_option_value_choice', array(
            'label'         => 'option_name',
            'option'        => $option,
            'property_path' => '[0]'
        ))->shouldBeCalled();

        $this->buildForm($builder, array(
            'options' => array($option)
        ));
    }

    function it_builds_a_form_using_option_name_as_label_if_presentation_is_empty(
        FormBuilderInterface $builder,
        OptionInterface $option
    ) {
        $option->getId()->shouldBeCalled()->willReturn(3);
        $option->getPresentation()->shouldBeCalled()->willReturn('option_presentation');
        $option->getName()->shouldNotBeCalled();


        $builder->add('3', 'sylius_varibale_name_option_value_choice', array(
            'label'         => 'option_presentation',
            'option'        => $option,
            'property_path' => '[0]'
        ))->shouldBeCalled();

        $this->buildForm($builder, array(
            'options' => array($option)
        ));
    }

    function it_has_options(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'options' => null
        ))->shouldBeCalled();

        $this->configureOptions($resolver);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_varibale_name_option_value_collection');
    }
}
