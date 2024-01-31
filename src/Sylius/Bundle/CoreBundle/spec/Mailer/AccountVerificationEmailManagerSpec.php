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
use Sylius\Bundle\CoreBundle\Mailer\AccountVerificationEmailManagerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\User\Model\UserInterface;

final class AccountVerificationEmailManagerSpec extends ObjectBehavior
{
    function let(SenderInterface $sender): void
    {
        $this->beConstructedWith($sender);
    }

    function it_implements_an_account_verification_email_manager_interface(): void
    {
        $this->shouldImplement(AccountVerificationEmailManagerInterface::class);
    }

    function it_sends_an_account_verification_email(
        SenderInterface $sender,
        ChannelInterface $channel,
        UserInterface $user,
    ): void {
        $user->getEmail()->willReturn('customer@example.com');

        $sender
            ->send(
                'account_verification_token',
                ['customer@example.com'],
                [
                    'user' => $user,
                    'localeCode' => 'en_US',
                    'channel' => $channel,
                ],
            )
            ->shouldBeCalled();

        $this->sendAccountVerificationEmail($user, $channel, 'en_US');
    }
}
