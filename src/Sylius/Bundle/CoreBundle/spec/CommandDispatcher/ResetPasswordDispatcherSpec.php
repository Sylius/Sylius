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

namespace spec\Sylius\Bundle\CoreBundle\CommandDispatcher;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Command\Admin\Account\ResetPassword;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class ResetPasswordDispatcherSpec extends ObjectBehavior
{
    function let(MessageBusInterface $messageBus): void
    {
        $this->beConstructedWith($messageBus);
    }

    function it_dispatches_a_reset_password_message(MessageBusInterface $messageBus): void
    {
        $message = new ResetPassword('token', 'password');

        $messageBus->dispatch($message)->willReturn(new Envelope($message))->shouldBeCalled();

        $this->dispatch('token', 'password');
    }
}
