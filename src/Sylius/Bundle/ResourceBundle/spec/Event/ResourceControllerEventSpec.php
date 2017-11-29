<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ResourceBundle\Event;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Symfony\Component\HttpFoundation\Response;

final class ResourceControllerEventSpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith('message');
    }

    public function it_stops_event_propagation(): void
    {
        $this->stop('message', ResourceControllerEvent::TYPE_SUCCESS, ['parameter']);
        $this->getMessageType()->shouldReturn(ResourceControllerEvent::TYPE_SUCCESS);
        $this->getMessageParameters()->shouldReturn(['parameter']);
        $this->getMessage()->shouldReturn('message');
        $this->isPropagationStopped()->shouldReturn(true);
    }

    public function it_check_if_an_error_has_been_detected(): void
    {
        $this->isStopped()->shouldReturn(false);
        $this->stop('message');
        $this->isStopped()->shouldReturn(true);
    }

    public function it_has_no_message_type_by_default(): void
    {
        $this->getMessageType()->shouldReturn('');
    }

    public function its_message_type_is_mutable(): void
    {
        $this->setMessageType(ResourceControllerEvent::TYPE_SUCCESS);
        $this->getMessageType()->shouldReturn(ResourceControllerEvent::TYPE_SUCCESS);
    }

    public function it_has_not_message_by_default(): void
    {
        $this->getMessage()->shouldReturn('');
    }

    public function its_message_is_mutable(): void
    {
        $this->setMessage('message');
        $this->getMessage()->shouldReturn('message');
    }

    public function it_has_empty_message_parameters_by_default(): void
    {
        $this->getMessageParameters()->shouldReturn([]);
    }

    public function its_message_parameter_is_mutable(): void
    {
        $this->setMessageParameters(['parameters']);
        $this->getMessageParameters()->shouldReturn(['parameters']);
    }

    public function it_has_response(): void
    {
        $response = new Response();

        $this->setResponse($response);

        $this->getResponse()->shouldReturn($response);
    }

    public function it_has_response_if_it_was_set_before(): void
    {
        $response = new Response();
        $this->setResponse($response);

        $this->hasResponse()->shouldReturn(true);
    }

    public function it_has_not_response_if_it_was_not_set_before(): void
    {
        $this->hasResponse()->shouldReturn(false);
    }
}
