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
use Sylius\Bundle\ApiBundle\Command\Account\VerifyCustomerAccount;
use Sylius\Calendar\Provider\DateTimeProviderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class VerifyCustomerAccountHandlerSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $shopUserRepository,
        DateTimeProviderInterface $dateTimeProvider,
    ): void {
        $this->beConstructedWith($shopUserRepository, $dateTimeProvider);
    }

    function it_is_a_message_handler(): void
    {
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    function it_verifies_shop_user(
        RepositoryInterface $shopUserRepository,
        DateTimeProviderInterface $dateTimeProvider,
        UserInterface $user,
    ): void {
        $shopUserRepository->findOneBy(['emailVerificationToken' => 'ToKeN'])->willReturn($user);
        $dateTimeProvider->now()->willReturn(new \DateTime());

        $user->setVerifiedAt(Argument::type(\DateTime::class))->shouldBeCalled();
        $user->setEmailVerificationToken(null)->shouldBeCalled();
        $user->enable()->shouldBeCalled();

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
