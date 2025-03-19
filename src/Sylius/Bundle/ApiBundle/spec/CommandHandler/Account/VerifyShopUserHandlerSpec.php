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
use spec\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;
use Sylius\Bundle\ApiBundle\Command\Account\SendAccountRegistrationEmail;
use Sylius\Bundle\ApiBundle\Command\Account\VerifyShopUser;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

final class VerifyShopUserHandlerSpec extends ObjectBehavior
{
    use MessageHandlerAttributeTrait;

    function let(
        RepositoryInterface $shopUserRepository,
        ClockInterface $clock,
        MessageBusInterface $commandBus,
    ): void {
        $this->beConstructedWith($shopUserRepository, $clock, $commandBus);
    }

    function it_verifies_shop_user(
        RepositoryInterface $shopUserRepository,
        ClockInterface $clock,
        UserInterface $user,
        MessageBusInterface $commandBus,
    ): void {
        $shopUserRepository->findOneBy(['emailVerificationToken' => 'ToKeN'])->willReturn($user);
        $clock->now()->willReturn(new \DateTimeImmutable());

        $user->getEmail()->willReturn('shop@example.com');
        $user->setVerifiedAt(Argument::type(\DateTimeImmutable::class))->shouldBeCalled();
        $user->setEmailVerificationToken(null)->shouldBeCalled();
        $user->enable()->shouldBeCalled();

        $commandBus->dispatch(
            new SendAccountRegistrationEmail('shop@example.com', 'en_US', 'WEB'),
            [new DispatchAfterCurrentBusStamp()],
        )->willReturn(new Envelope(new \stdClass()));

        $this(new VerifyShopUser(channelCode: 'WEB', localeCode:  'en_US', token: 'ToKeN'));
    }

    function it_throws_error_if_user_does_not_exist(
        RepositoryInterface $shopUserRepository,
    ): void {
        $shopUserRepository->findOneBy(['emailVerificationToken' => 'ToKeN'])->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [new VerifyShopUser(channelCode: 'WEB', localeCode:  'en_US', token: 'ToKeN')])
        ;
    }
}
