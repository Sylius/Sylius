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
use Sylius\Bundle\ApiBundle\Command\Account\RequestResetPasswordToken;
use Sylius\Bundle\ApiBundle\Command\Account\SendResetPasswordEmail;
use Sylius\Calendar\Provider\DateTimeProviderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

final class RequestResetPasswordTokenHandlerSpec extends ObjectBehavior
{
    function let(
        UserRepositoryInterface $userRepository,
        MessageBusInterface $messageBus,
        GeneratorInterface $generator,
        DateTimeProviderInterface $dateTimeProvider,
    ): void {
        $this->beConstructedWith($userRepository, $messageBus, $generator, $dateTimeProvider);
    }

    function it_is_a_message_handler(): void
    {
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    function it_handles_request_for_password_reset_token(
        UserRepositoryInterface $userRepository,
        ShopUserInterface $shopUser,
        GeneratorInterface $generator,
        DateTimeProviderInterface $dateTimeProvider,
        MessageBusInterface $messageBus,
    ): void {
        $userRepository->findOneByEmail('test@email.com')->willReturn($shopUser);
        $dateTimeProvider->now()->willReturn(new \DateTime());

        $generator->generate()->willReturn('TOKEN');
        $shopUser->setPasswordResetToken('TOKEN')->shouldBeCalled();
        $shopUser->setPasswordRequestedAt(Argument::type(\DateTime::class));

        $sendResetPasswordEmail = new SendResetPasswordEmail('test@email.com', 'WEB', 'en_US');

        $messageBus->dispatch(
            $sendResetPasswordEmail,
            [new DispatchAfterCurrentBusStamp()],
        )->willReturn(new Envelope($sendResetPasswordEmail))->shouldBeCalled();

        $requestResetPasswordToken = new RequestResetPasswordToken('test@email.com');
        $requestResetPasswordToken->setChannelCode('WEB');
        $requestResetPasswordToken->setLocaleCode('en_US');

        $this($requestResetPasswordToken);
    }

    function it_does_nothing_when_shop_user_has_not_been_found(
        UserRepositoryInterface $userRepository,
        MessageBusInterface $messageBus,
    ): void {
        $userRepository->findOneByEmail('test@email.com')->willReturn(null);

        $messageBus->dispatch(Argument::any())->shouldNotBeCalled();

        $requestResetPasswordToken = new RequestResetPasswordToken('test@email.com');
        $requestResetPasswordToken->setChannelCode('WEB');
        $requestResetPasswordToken->setLocaleCode('en_US');

        $this($requestResetPasswordToken);
    }
}
