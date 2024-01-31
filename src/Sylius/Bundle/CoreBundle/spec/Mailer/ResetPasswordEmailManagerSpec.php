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

namespace spec\Sylius\Bundle\CoreBundle\Mailer;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Mailer\ResetPasswordEmailManagerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\User\Model\UserInterface;

final class ResetPasswordEmailManagerSpec extends ObjectBehavior
{
    function let(SenderInterface $sender): void
    {
        $this->beConstructedWith($sender);
    }

    function it_implements_a_reset_password_email_manager_interface(): void
    {
        $this->shouldImplement(ResetPasswordEmailManagerInterface::class);
    }

    function it_sends_a_reset_password_email(
        SenderInterface $sender,
        UserInterface $user,
        ChannelInterface $channel,
    ): void {
        $user->getEmail()->willReturn('customer@example.com');

        $sender
            ->send(
                'password_reset',
                ['customer@example.com'],
                [
                    'user' => $user,
                    'localeCode' => 'en_US',
                    'channel' => $channel,
                ],
            )
            ->shouldBeCalled()
        ;

        $this->sendResetPasswordEmail($user, $channel, 'en_US');
    }
}
