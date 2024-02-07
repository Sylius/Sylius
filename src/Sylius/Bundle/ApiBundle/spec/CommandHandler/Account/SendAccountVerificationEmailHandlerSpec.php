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
use Sylius\Bundle\ApiBundle\Command\Account\SendAccountVerificationEmail;
use Sylius\Bundle\ApiBundle\Exception\ChannelNotFoundException;
use Sylius\Bundle\ApiBundle\Exception\UserNotFoundException;
use Sylius\Bundle\CoreBundle\Mailer\AccountVerificationEmailManagerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;

final class SendAccountVerificationEmailHandlerSpec extends ObjectBehavior
{
    function let(
        AccountVerificationEmailManagerInterface $accountVerificationEmailManager,
        UserRepositoryInterface $shopUserRepository,
        ChannelRepositoryInterface $channelRepository,
    ): void {
        $this->beConstructedWith($accountVerificationEmailManager, $shopUserRepository, $channelRepository);
    }

    function it_sends_user_account_verification_email(
        AccountVerificationEmailManagerInterface $accountVerificationEmailManager,
        UserRepositoryInterface $shopUserRepository,
        ChannelRepositoryInterface $channelRepository,
        ShopUserInterface $shopUser,
        ChannelInterface $channel,
    ): void {
        $shopUserRepository->findOneByEmail('shop@example.com')->willReturn($shopUser);
        $channelRepository->findOneByCode('WEB')->willReturn($channel);

        $channel->isAccountVerificationRequired()->willReturn(false);

        $accountVerificationEmailManager
            ->sendAccountVerificationEmail($shopUser, $channel, 'en_US')
            ->shouldBeCalled()
        ;

        $this(new SendAccountVerificationEmail('shop@example.com', 'en_US', 'WEB'));
    }

    function it_throws_an_exception_if_user_has_not_been_found(
        AccountVerificationEmailManagerInterface $accountVerificationEmailManager,
        UserRepositoryInterface $shopUserRepository,
        ChannelRepositoryInterface $channelRepository,
    ): void {
        $shopUserRepository->findOneByEmail('shop@example.com')->willReturn(null);
        $channelRepository->findOneByCode('WEB')->shouldNotBeCalled();
        $accountVerificationEmailManager->sendAccountVerificationEmail(Argument::cetera())->shouldNotBeCalled();

        $this
            ->shouldThrow(UserNotFoundException::class)
            ->during(
                '__invoke',
                [new SendAccountVerificationEmail('shop@example.com', 'en_US', 'WEB')],
            );
    }

    function it_throws_an_exception_if_channel_has_not_been_found(
        AccountVerificationEmailManagerInterface $accountVerificationEmailManager,
        UserRepositoryInterface $shopUserRepository,
        ChannelRepositoryInterface $channelRepository,
        ShopUserInterface $shopUser,
    ): void {
        $shopUserRepository->findOneByEmail('shop@example.com')->willReturn($shopUser);
        $channelRepository->findOneByCode('WEB')->willReturn(null);
        $accountVerificationEmailManager->sendAccountVerificationEmail(Argument::cetera())->shouldNotBeCalled();

        $this
            ->shouldThrow(ChannelNotFoundException::class)
            ->during(
                '__invoke',
                [new SendAccountVerificationEmail('shop@example.com', 'en_US', 'WEB')],
            );
    }
}
