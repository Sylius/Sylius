<?php

namespace spec\Sylius\Component\Attribute\Model;

use Sylius\Component\Attribute\Model\AttributeSelectOption;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Attribute\Model\AttributeSelectOptionInterface;

class AttributeSelectOptionSpec extends ObjectBehavior
{

    function let(): void
    {
        $this->setCurrentLocale('en_US');
        $this->setFallbackLocale('en_US');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AttributeSelectOption::class);
    }

    function it_implements_attribute_select_option_interface(): void
    {
        $this->shouldImplement(AttributeSelectOptionInterface::class);
    }

    function it_has_no_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_name_by_default(): void
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_is_mutable(): void
    {
        $this->setName('Size XL');
        $this->getName()->shouldReturn('Size XL');
    }

    function it_returns_name_when_converted_to_string(): void
    {
        $this->setName('Size L');
        $this->__toString()->shouldReturn('Size L');
    }
}
