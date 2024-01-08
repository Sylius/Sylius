<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\MessageDispatcher;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Message\Admin\ResendOrderConfirmationEmail;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class ResendOrderConfirmationEmailDispatcherSpec extends ObjectBehavior
{
    function let(MessageBusInterface $messageBus): void
    {
        $this->beConstructedWith($messageBus);
    }

    function it_dispatches_a_resend_confirmation_email(MessageBusInterface $messageBus): void
    {
        $message = new ResendOrderConfirmationEmail('token');

        $messageBus->dispatch($message)->willReturn(new Envelope($message))->shouldBeCalled();

        $this->dispatch('token');
    }
}
