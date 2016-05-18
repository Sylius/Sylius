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
use Sylius\Component\Variation\Model\OptionInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OptionValueChoiceTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('variable_name');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\VariationBundle\Form\Type\OptionValueChoiceType');
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement(FormTypeInterface::class);
    }

    function it_has_options(OptionsResolver $resolver)
    {
        $resolver->setDefaults(Argument::withKey('choice_list'))->shouldBeCalled()->willReturn($resolver);

        $resolver->setRequired([
            'option',
        ])->shouldBeCalled()->willReturn($resolver);

        $resolver->addAllowedTypes([
            'option' => OptionInterface::class,
        ])->shouldBeCalled()->willReturn($resolver);

        $this->configureOptions($resolver);
    }

    function it_has_a_parent()
    {
        $this->getParent()->shouldReturn('choice');
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_variable_name_option_value_choice');
    }
}
