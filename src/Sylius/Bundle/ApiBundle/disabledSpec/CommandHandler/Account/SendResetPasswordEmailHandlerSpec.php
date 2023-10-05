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

namespace spec\Sylius\Bundle\ApiBundle\CommandHandler\Account;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\Account\SendResetPasswordEmail;
use Sylius\Bundle\CoreBundle\Mailer\Emails;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class SendResetPasswordEmailHandlerSpec extends ObjectBehavior
{
    function let(
        SenderInterface $emailSender,
        ChannelRepositoryInterface $channelRepository,
        UserRepositoryInterface $userRepository,
    ): void {
        $this->beConstructedWith($emailSender, $channelRepository, $userRepository);
    }

    function it_is_a_message_handler(): void
    {
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    function it_sends_message_with_reset_password_token(
        SenderInterface $sender,
        UserRepositoryInterface $userRepository,
        SendResetPasswordEmail $sendResetPasswordEmail,
        UserInterface $user,
        ChannelRepositoryInterface $channelRepository,
        ChannelInterface $channel,
    ): void {
        $sendResetPasswordEmail->email()->willReturn('iAmAnEmail@spaghettiCode.php');

        $userRepository->findOneByEmail('iAmAnEmail@spaghettiCode.php')->willReturn($user);

        $sendResetPasswordEmail->channelCode()->willReturn('WEB');

        $channelRepository->findOneByCode('WEB')->willReturn($channel);

        $sendResetPasswordEmail->localeCode()->willReturn('en_US');

        $sender->send(
            Emails::PASSWORD_RESET,
            ['iAmAnEmail@spaghettiCode.php'],
            [
                'user' => $user->getWrappedObject(),
                'localeCode' => 'en_US',
                'channel' => $channel->getWrappedObject(),
            ],
        );

        $this(new SendResetPasswordEmail('iAmAnEmail@spaghettiCode.php', 'WEB', 'en_US'));
    }
}
