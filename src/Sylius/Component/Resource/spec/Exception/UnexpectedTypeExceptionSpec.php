<?php

namespace spec\Sylius\Component\Resource\Exception;

use PhpSpec\ObjectBehavior;

class UnexpectedTypeExceptionSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('stringValue', '\ExpectedType');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Resource\Exception\UnexpectedTypeException');
    }

    function it_extends_invalid_argument_exception()
    {
        $this->shouldHaveType(\InvalidArgumentException::class);
    }

    function it_has_a_message()
    {
        $this->getMessage()->shouldReturn('Expected argument of type "\ExpectedType", "string" given.');
    }
}
