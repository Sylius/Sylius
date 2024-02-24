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
use Sylius\Bundle\CoreBundle\Mailer\AccountRegistrationEmailManagerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;

final class SendAccountRegistrationEmailHandlerSpec extends ObjectBehavior
{
    function let(
        UserRepositoryInterface $shopUserRepository,
        ChannelRepositoryInterface $channelRepository,
        AccountRegistrationEmailManagerInterface $accountRegistrationEmailManager,
    ): void {
        $this->beConstructedWith($shopUserRepository, $channelRepository, $accountRegistrationEmailManager);
    }

    function it_sends_user_account_registration_email_when_account_verification_is_not_required(
        UserRepositoryInterface $shopUserRepository,
        ChannelRepositoryInterface $channelRepository,
        AccountRegistrationEmailManagerInterface $accountRegistrationEmailManager,
        ShopUserInterface $shopUser,
        ChannelInterface $channel,
    ): void {
        $shopUserRepository->findOneByEmail('shop@example.com')->willReturn($shopUser);
        $channelRepository->findOneByCode('WEB')->willReturn($channel);

        $channel->isAccountVerificationRequired()->willReturn(false);

        $accountRegistrationEmailManager
            ->sendAccountRegistrationEmail($shopUser, $channel, 'en_US')
            ->shouldBeCalled()
        ;

        $this(new SendAccountRegistrationEmail('shop@example.com', 'en_US', 'WEB'));
    }

    function it_sends_user_registration_email_when_account_verification_required_and_user_is_enabled(
        UserRepositoryInterface $shopUserRepository,
        ChannelRepositoryInterface $channelRepository,
        AccountRegistrationEmailManagerInterface $accountRegistrationEmailManager,
        ShopUserInterface $shopUser,
        ChannelInterface $channel,
    ): void {
        $shopUserRepository->findOneByEmail('shop@example.com')->willReturn($shopUser);
        $channelRepository->findOneByCode('WEB')->willReturn($channel);

        $channel->isAccountVerificationRequired()->willReturn(true);
        $shopUser->isEnabled()->willReturn(true);

        $accountRegistrationEmailManager
            ->sendAccountRegistrationEmail($shopUser, $channel, 'en_US')
            ->shouldBeCalled()
        ;

        $this(new SendAccountRegistrationEmail('shop@example.com', 'en_US', 'WEB'));
    }

    function it_does_nothing_when_account_verification_is_required_and_user_is_disabled(
        UserRepositoryInterface $shopUserRepository,
        ChannelRepositoryInterface $channelRepository,
        AccountRegistrationEmailManagerInterface $accountRegistrationEmailManager,
        ShopUserInterface $shopUser,
        ChannelInterface $channel,
    ): void {
        $shopUserRepository->findOneByEmail('shop@example.com')->willReturn($shopUser);
        $channelRepository->findOneByCode('WEB')->willReturn($channel);

        $channel->isAccountVerificationRequired()->willReturn(true);
        $shopUser->isEnabled()->willReturn(false);

        $accountRegistrationEmailManager->sendAccountRegistrationEmail(Argument::cetera())->shouldNotBeCalled();

        $this(new SendAccountRegistrationEmail('shop@example.com', 'en_US', 'WEB'));
    }
}
