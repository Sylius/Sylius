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
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Command\Account\SendAccountRegistrationEmail;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;

final class SendAccountRegistrationEmailHandlerSpec extends ObjectBehavior
{
    function let(
        UserRepositoryInterface $shopUserRepository,
        ChannelRepositoryInterface $channelRepository,
        SenderInterface $emailSender,
    ): void {
        $this->beConstructedWith($shopUserRepository, $channelRepository, $emailSender);
    }

    function it_sends_user_registration_email_when_account_verification_is_not_required(
        UserRepositoryInterface $shopUserRepository,
        ChannelRepositoryInterface $channelRepository,
        SenderInterface $emailSender,
        ShopUserInterface $shopUser,
        ChannelInterface $channel,
    ): void {
        $shopUserRepository->findOneByEmail('shop@example.com')->willReturn($shopUser);
        $channelRepository->findOneByCode('WEB')->willReturn($channel);

        $channel->isAccountVerificationRequired()->willReturn(false);

        $emailSender->send(
            'user_registration',
            ['shop@example.com'],
            [
                'user' => $shopUser,
                'localeCode' => 'en_US',
                'channel' => $channel,
            ],
        )->shouldBeCalled();

        $this(new SendAccountRegistrationEmail('shop@example.com', 'en_US', 'WEB'));
    }

    function it_sends_user_registration_email_when_account_verification_required_and_user_is_verified(
        UserRepositoryInterface $shopUserRepository,
        ChannelRepositoryInterface $channelRepository,
        SenderInterface $emailSender,
        ShopUserInterface $shopUser,
        ChannelInterface $channel,
    ): void {
        $shopUserRepository->findOneByEmail('shop@example.com')->willReturn($shopUser);
        $channelRepository->findOneByCode('WEB')->willReturn($channel);

        $channel->isAccountVerificationRequired()->willReturn(true);
        $shopUser->isEnabled()->willReturn(true);

        $emailSender->send(
            'user_registration',
            ['shop@example.com'],
            [
                'user' => $shopUser,
                'localeCode' => 'en_US',
                'channel' => $channel,
            ],
        )->shouldBeCalled();

        $this(new SendAccountRegistrationEmail('shop@example.com', 'en_US', 'WEB'));
    }

    function it_does_nothing_when_account_verification_required_and_user_is_not_verified(
        UserRepositoryInterface $shopUserRepository,
        ChannelRepositoryInterface $channelRepository,
        SenderInterface $emailSender,
        ShopUserInterface $shopUser,
        ChannelInterface $channel,
    ): void {
        $shopUserRepository->findOneByEmail('shop@example.com')->willReturn($shopUser);
        $channelRepository->findOneByCode('WEB')->willReturn($channel);

        $channel->isAccountVerificationRequired()->willReturn(true);
        $shopUser->isEnabled()->willReturn(false);

        $emailSender->send(Argument::any(), Argument::any(), Argument::any())->shouldNotBeCalled();

        $this(new SendAccountRegistrationEmail('shop@example.com', 'en_US', 'WEB'));
    }
}
