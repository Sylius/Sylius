<?php

namespace spec\Sylius\Component\Resource\Event;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Resource\Event\ResourceEvent;

class ResourceEventSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('message');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Resource\Event\ResourceEvent');
    }

    function it_stops_event_propagation()
    {
        $this->stop('message', ResourceEvent::TYPE_SUCCESS, array('parameter'));
        $this->getMessageType()->shouldReturn(ResourceEvent::TYPE_SUCCESS);
        $this->getMessageParameters()->shouldReturn(array('parameter'));
        $this->getMessage()->shouldReturn('message');
        $this->isPropagationStopped()->shouldReturn(true);
    }

    function it_check_if_an_error_has_been_detected()
    {
        $this->isStopped()->shouldReturn(false);
        $this->stop('message');
        $this->isStopped()->shouldReturn(true);
    }

    function it_has_not_message_type_by_default()
    {
        $this->getMessageType()->shouldReturn('');
    }

    function its_message_type_is_mutable()
    {
        $this->setMessageType(ResourceEvent::TYPE_SUCCESS)->shouldReturn($this);
        $this->getMessageType()->shouldReturn(ResourceEvent::TYPE_SUCCESS);
    }

    function it_has_not_message_by_default()
    {
        $this->getMessage()->shouldReturn('');
    }

    function its_message_is_mutable()
    {
        $this->setMessage('message')->shouldReturn($this);
        $this->getMessage()->shouldReturn('message');
    }

    function it_has_not_message_parameter_by_default()
    {
        $this->getMessageParameters()->shouldReturn(array());
    }

    function its_message_parameter_is_mutable()
    {
        $this->setMessageParameters(array('parameters'))->shouldReturn($this);
        $this->getMessageParameters()->shouldReturn(array('parameters'));
    }

}
