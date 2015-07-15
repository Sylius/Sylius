<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Variation\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Variation\Model\OptionInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OptionValueSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Variation\Model\OptionValue');
    }

    public function it_is_a_Sylius_product_option_value()
    {
        $this->shouldImplement('Sylius\Component\Variation\Model\OptionValueInterface');
    }

    public function it_should_not_have_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    public function it_should_not_belong_to_an_option_by_default()
    {
        $this->getOption()->shouldReturn(null);
    }

    public function it_should_allow_assigning_itself_to_an_option(OptionInterface $option)
    {
        $this->setOption($option);
        $this->getOption()->shouldReturn($option);
    }

    public function it_should_allow_detaching_itself_from_an_option(OptionInterface $option)
    {
        $this->setOption($option);
        $this->getOption()->shouldReturn($option);

        $this->setOption(null);
        $this->getOption()->shouldReturn(null);
    }

    public function it_should_not_have_value_by_default()
    {
        $this->getValue()->shouldReturn(null);
    }

    public function its_value_should_be_mutable()
    {
        $this->setValue('XXL');
        $this->getValue()->shouldReturn('XXL');
    }

    public function it_returns_its_value_when_converted_to_string()
    {
        $this->setValue('S');
        $this->__toString()->shouldReturn('S');
    }

    public function it_throws_exception_when_trying_to_get_name_without_option_being_assigned()
    {
        $this
            ->shouldThrow('BadMethodCallException')
            ->duringGetName()
        ;
    }

    public function it_returns_its_option_name(OptionInterface $option)
    {
        $option->getName()->willReturn('T-Shirt size');
        $this->setOption($option);

        $this->getName()->shouldReturn('T-Shirt size');
    }

    public function it_throws_exception_when_trying_to_get_presentation_without_option_being_assigned()
    {
        $this
            ->shouldThrow('BadMethodCallException')
            ->duringGetPresentation()
        ;
    }

    public function it_returns_its_option_presentation(OptionInterface $option)
    {
        $option->getPresentation()->willReturn('Size');
        $this->setOption($option);

        $this->getPresentation()->shouldReturn('Size');
    }
}
