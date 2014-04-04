<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AddressingBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class CountryEntityChoiceTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('Country');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Form\Type\CountryEntityChoiceType');
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    function it_is_a_Sylius_country_choice_type()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Form\Type\CountryChoiceType');
    }

    function it_has_a_valid_name()
    {
        $this->getName()->shouldReturn('sylius_country_choice');
    }

    function it_has_a_parent_type()
    {
        $this->getParent()->shouldReturn('entity');
    }

    function it_defines_assigned_data_class(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(
                array(
                    'class' => 'Country',
                )
            )
            ->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }
}
