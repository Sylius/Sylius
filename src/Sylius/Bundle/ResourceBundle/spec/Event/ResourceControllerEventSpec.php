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
use Sylius\Component\Resource\Event\ResourceEvent;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class ResourceControllerEventSpec extends ObjectBehavior
{
    function let(
        RequestConfiguration $requestConfiguration
    ) {
        $this->beConstructedWith('message', $requestConfiguration);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent');
    }

    function it_stops_event_propagation()
    {
        $this->stop('message', ResourceEvent::TYPE_SUCCESS, ['parameter']);
        $this->getMessageType()->shouldReturn(ResourceEvent::TYPE_SUCCESS);
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
        $this->setMessageType(ResourceEvent::TYPE_SUCCESS);
        $this->getMessageType()->shouldReturn(ResourceEvent::TYPE_SUCCESS);
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

    function it_should_return_the_request_configuration(
        RequestConfiguration $requestConfiguration
    ) {
        $this->getRequestConfiguration()->shouldReturn($requestConfiguration);
    }
}
