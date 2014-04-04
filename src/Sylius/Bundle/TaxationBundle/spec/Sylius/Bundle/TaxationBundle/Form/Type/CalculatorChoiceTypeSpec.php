<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\TaxationBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class CalculatorChoiceTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(array('calc1', 'calc2'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\TaxationBundle\Form\Type\CalculatorChoiceType');
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    function it_has_a_valid_name()
    {
        $this->getName()->shouldReturn('sylius_tax_calculator_choice');
    }

    function it_has_a_parent_type()
    {
        $this->getParent()->shouldReturn('choice');
    }

    function it_defines_assigned_data_class(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(
                array(
                    'choices' => array('calc1', 'calc2'),
                )
            )
            ->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }
}
