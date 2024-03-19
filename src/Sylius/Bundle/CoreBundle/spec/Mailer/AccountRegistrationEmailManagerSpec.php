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
use Sylius\Bundle\CoreBundle\Mailer\AccountRegistrationEmailManagerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\User\Model\UserInterface;

final class AccountRegistrationEmailManagerSpec extends ObjectBehavior
{
    function let(SenderInterface $sender): void
    {
        $this->beConstructedWith($sender);
    }

    function it_implements_an_account_registration_email_manager_interface(): void
    {
        $this->shouldImplement(AccountRegistrationEmailManagerInterface::class);
    }

    function it_sends_an_account_registration_email(
        SenderInterface $sender,
        UserInterface $user,
        ChannelInterface $channel,
    ): void {
        $user->getEmail()->willReturn('customer@example.com');

        $sender
            ->send(
                'user_registration',
                ['customer@example.com'],
                [
                    'user' => $user,
                    'localeCode' => 'en_US',
                    'channel' => $channel,
                ],
                [],
                ['customer@example.com'],
            )
            ->shouldBeCalled();

        $this->sendAccountRegistrationEmail($user, $channel, 'en_US');
    }
}
