<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\VariableProductBundle\Model;

use PhpSpec\ObjectBehavior;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OptionValueSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\VariableProductBundle\Model\OptionValue');
    }

    function it_is_a_Sylius_product_option_value()
    {
        $this->shouldImplement('Sylius\Bundle\VariableProductBundle\Model\OptionValueInterface');
    }

    function it_should_not_have_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_should_not_belong_to_an_option_by_default()
    {
        $this->getOption()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\VariableProductBundle\Model\OptionInterface $option
     */
    function it_should_allow_assigning_itself_to_an_option($option)
    {
        $this->setOption($option);
        $this->getOption()->shouldReturn($option);
    }

    /**
     * @param Sylius\Bundle\VariableProductBundle\Model\OptionInterface $option
     */
    function it_should_allow_detaching_itself_from_an_option($option)
    {
        $this->setOption($option);
        $this->getOption()->shouldReturn($option);

        $this->setOption(null);
        $this->getOption()->shouldReturn(null);
    }

    function it_should_not_have_value_by_default()
    {
        $this->getValue()->shouldReturn(null);
    }

    function its_value_should_be_mutable()
    {
        $this->setValue('XXL');
        $this->getValue()->shouldReturn('XXL');
    }

    function it_returns_its_value_when_converted_to_string()
    {
        $this->setValue('S');
        $this->__toString()->shouldReturn('S');
    }

    function it_throws_exception_when_trying_to_get_name_without_option_being_assigned()
    {
        $this
            ->shouldThrow('BadMethodCallException')
            ->duringGetName()
        ;
    }

    /**
     * @param Sylius\Bundle\VariableProductBundle\Model\OptionInterface $option
     */
    function it_returns_its_option_name($option)
    {
        $option->getName()->willReturn('T-Shirt size');
        $this->setOption($option);

        $this->getName()->shouldReturn('T-Shirt size');
    }

    function it_throws_exception_when_trying_to_get_presentation_without_option_being_assigned()
    {
        $this
            ->shouldThrow('BadMethodCallException')
            ->duringGetPresentation()
        ;
    }

    /**
     * @param Sylius\Bundle\VariableProductBundle\Model\OptionInterface $option
     */
    function it_returns_its_option_presentation($option)
    {
        $option->getPresentation()->willReturn('Size');
        $this->setOption($option);

        $this->getPresentation()->shouldReturn('Size');
    }
}
