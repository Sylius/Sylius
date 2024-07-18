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
use Sylius\Bundle\CoreBundle\Mailer\ResetPasswordEmailManagerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class SendResetPasswordEmailHandlerSpec extends ObjectBehavior
{
    function let(
        ChannelRepositoryInterface $channelRepository,
        UserRepositoryInterface $userRepository,
        ResetPasswordEmailManagerInterface $resetPasswordEmailManager,
    ): void {
        $this->beConstructedWith($channelRepository, $userRepository, $resetPasswordEmailManager);
    }

    function it_is_a_message_handler(): void
    {
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    function it_sends_message_with_reset_password_token(
        ChannelRepositoryInterface $channelRepository,
        UserRepositoryInterface $userRepository,
        ResetPasswordEmailManagerInterface $resetPasswordEmailManager,
        UserInterface $user,
        ChannelInterface $channel,
    ): void {
        $userRepository->findOneByEmail('iAmAnEmail@spaghettiCode.php')->willReturn($user);

        $channelRepository->findOneByCode('WEB')->willReturn($channel);

        $resetPasswordEmailManager->sendResetPasswordEmail($user, $channel, 'en_US')->shouldBeCalled();

        $this(new SendResetPasswordEmail('iAmAnEmail@spaghettiCode.php', 'WEB', 'en_US'));
    }
}
