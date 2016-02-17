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
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CalculatorChoiceTypeSpec extends ObjectBehavior
{
    function let()
    {
        $choices = [
            'flat_rate' => 'Flat rate per shipment',
            'per_unit_rate' => 'Per unit rate',
        ];

        $this->beConstructedWith($choices);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Form\Type\CalculatorChoiceType');
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement(FormTypeInterface::class);
    }

    function it_defines_calculator_choices(OptionsResolver $resolver)
    {
        $choices = [
            'flat_rate' => 'Flat rate per shipment',
            'per_unit_rate' => 'Per unit rate',
        ];

        $resolver->setDefaults(['choices' => $choices])->shouldBeCalled();

        $this->configureOptions($resolver);
    }
}
