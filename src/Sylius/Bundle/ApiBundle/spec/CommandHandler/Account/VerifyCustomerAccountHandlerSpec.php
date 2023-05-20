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
use Sylius\Bundle\ApiBundle\Command\Account\VerifyCustomerAccount;
use Sylius\Calendar\Provider\DateTimeProviderInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

final class VerifyCustomerAccountHandlerSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $shopUserRepository,
        DateTimeProviderInterface $dateTimeProvider,
        MessageBusInterface $commandBus,
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext,
    ): void {
        $this->beConstructedWith($shopUserRepository, $dateTimeProvider, $commandBus, $channelContext, $localeContext);
    }

    function it_is_a_message_handler(): void
    {
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    function it_verifies_shop_user(
        RepositoryInterface $shopUserRepository,
        DateTimeProviderInterface $dateTimeProvider,
        UserInterface $user,
        MessageBusInterface $commandBus,
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext,
        ChannelInterface $channel,
    ): void {
        $shopUserRepository->findOneBy(['emailVerificationToken' => 'ToKeN'])->willReturn($user);
        $dateTimeProvider->now()->willReturn(new \DateTime());

        $user->getEmail()->willReturn('shop@example.com');
        $user->setVerifiedAt(Argument::type(\DateTime::class))->shouldBeCalled();
        $user->setEmailVerificationToken(null)->shouldBeCalled();
        $user->enable()->shouldBeCalled();

        $channel->getCode()->willReturn('WEB');

        $channelContext->getChannel()->willReturn($channel);
        $localeContext->getLocaleCode()->willReturn('en_US');

        $commandBus->dispatch(
            new SendAccountRegistrationEmail('shop@example.com', 'en_US', 'WEB'),
            [new DispatchAfterCurrentBusStamp()]
        )->willReturn(new Envelope(new \stdClass()));

        $this(new VerifyCustomerAccount('ToKeN'));
    }

    function it_throws_error_if_user_does_not_exist(
        RepositoryInterface $shopUserRepository,
    ): void {
        $shopUserRepository->findOneBy(['emailVerificationToken' => 'ToKeN'])->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [new VerifyCustomerAccount('ToKeN')])
        ;
    }
}
