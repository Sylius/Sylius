<?php

namespace spec\Sylius\Component\Resource\Exception;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class UnexpectedTypeExceptionSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('givenType', 'expectedType');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Resource\Exception\UnexpectedTypeException');
    }

    function it_extends_invalid_argument_exception()
    {
        $this->shouldHaveType('\InvalidArgumentException');
    }
}
