<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\Event;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;

/**
 * @mixin ResourceControllerEvent
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class ResourceControllerEventSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('message');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ResourceControllerEvent::class);
    }

    function it_stops_event_propagation()
    {
        $this->stop('message', ResourceControllerEvent::TYPE_SUCCESS, ['parameter']);
        $this->getMessageType()->shouldReturn(ResourceControllerEvent::TYPE_SUCCESS);
        $this->getMessageParameters()->shouldReturn(['parameter']);
        $this->getMessage()->shouldReturn('message');
        $this->isPropagationStopped()->shouldReturn(true);
    }

    function it_check_if_an_error_has_been_detected()
    {
        $this->isStopped()->shouldReturn(false);
        $this->stop('message');
        $this->isStopped()->shouldReturn(true);
    }

    function it_has_no_message_type_by_default()
    {
        $this->getMessageType()->shouldReturn('');
    }

    function its_message_type_is_mutable()
    {
        $this->setMessageType(ResourceControllerEvent::TYPE_SUCCESS);
        $this->getMessageType()->shouldReturn(ResourceControllerEvent::TYPE_SUCCESS);
    }

    function it_has_not_message_by_default()
    {
        $this->getMessage()->shouldReturn('');
    }

    function its_message_is_mutable()
    {
        $this->setMessage('message');
        $this->getMessage()->shouldReturn('message');
    }

    function it_has_empty_message_parameters_by_default()
    {
        $this->getMessageParameters()->shouldReturn([]);
    }

    function its_message_parameter_is_mutable()
    {
        $this->setMessageParameters(['parameters']);
        $this->getMessageParameters()->shouldReturn(['parameters']);
    }
}
