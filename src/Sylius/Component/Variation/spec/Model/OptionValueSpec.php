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
use Sylius\Component\Variation\Model\OptionValueInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OptionValueSpec extends ObjectBehavior
{
    public function let()
    {
        $this->setCurrentLocale('en_US');
        $this->setFallbackLocale('en_US');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Variation\Model\OptionValue');
    }

    function it_is_a_Sylius_product_option_value()
    {
        $this->shouldImplement(OptionValueInterface::class);
    }

    function it_should_not_have_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_mutable_code()
    {
        $this->setCode('OV1');
        $this->getCode()->shouldReturn('OV1');
    }

    function it_should_not_belong_to_an_option_by_default()
    {
        $this->getOption()->shouldReturn(null);
    }

    function it_should_allow_assigning_itself_to_an_option(OptionInterface $option)
    {
        $this->setOption($option);
        $this->getOption()->shouldReturn($option);
    }

    function it_should_allow_detaching_itself_from_an_option(OptionInterface $option)
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

    function it_throws_exception_when_trying_to_get_option_code_without_option_being_assigned()
    {
        $this
            ->shouldThrow(\BadMethodCallException::class)
            ->duringGetOptionCode()
        ;
    }

    function it_returns_its_option_code(OptionInterface $option)
    {
        $option->getCode()->willReturn('01');
        $this->setOption($option);

        $this->getOptionCode()->shouldReturn('01');
    }

    function it_throws_exception_when_trying_to_get_presentation_without_option_being_assigned()
    {
        $this
            ->shouldThrow(\BadMethodCallException::class)
            ->duringGetPresentation()
        ;
    }

    function it_returns_its_option_presentation(OptionInterface $option)
    {
        $option->getName()->willReturn('Size');
        $this->setOption($option);

        $this->getPresentation()->shouldReturn('Size');
    }
}
