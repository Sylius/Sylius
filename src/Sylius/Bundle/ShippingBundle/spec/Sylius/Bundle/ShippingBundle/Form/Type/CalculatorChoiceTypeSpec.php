<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ShippingBundle\Form\Type;

use PhpSpec\ObjectBehavior;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CalculatorChoiceTypeSpec extends ObjectBehavior
{
    function let()
    {
        $choices = array(
            'flat_rate'     => 'Flat rate per shipment',
            'per_item_rate' => 'Per item rate'
        );

        $this->beConstructedWith($choices);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Form\Type\CalculatorChoiceType');
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    /**
     * @param Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    function it_defines_calculator_choices($resolver)
    {
        $choices = array(
            'flat_rate'     => 'Flat rate per shipment',
            'per_item_rate' => 'Per item rate'
        );

        $resolver->setDefaults(array('choices' => $choices))->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }
}
