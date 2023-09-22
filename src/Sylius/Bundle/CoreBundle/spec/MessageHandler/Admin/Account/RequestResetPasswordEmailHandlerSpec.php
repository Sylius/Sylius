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

namespace spec\Sylius\Bundle\CoreBundle\MessageHandler\Admin\Account;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Message\Admin\Account\RequestResetPasswordEmail;
use Sylius\Bundle\CoreBundle\Message\Admin\Account\SendResetPasswordEmail;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

final class RequestResetPasswordEmailHandlerSpec extends ObjectBehavior
{
    public function let(
        UserRepositoryInterface $userRepository,
        GeneratorInterface $generator,
        ClockInterface $clock,
        MessageBusInterface $messageBus,
    ): void {
        $this->beConstructedWith($userRepository, $generator, $clock, $messageBus);
    }

    public function it_is_a_message_handler(): void
    {
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    public function it_handles_request_for_password_reset_token(
        UserRepositoryInterface $userRepository,
        GeneratorInterface $generator,
        ClockInterface $clock,
        MessageBusInterface $messageBus,
        AdminUserInterface $adminUser,
    ): void {
        $userRepository->findOneByEmail('admin@example.com')->willReturn($adminUser);

        $generator->generate()->willReturn('sometoken');

        $now = new \DateTimeImmutable();
        $clock->now()->willReturn($now);

        $adminUser->getEmail()->willReturn('admin@example.com');
        $adminUser->getLocaleCode()->willReturn('en_US');
        $adminUser->setPasswordResetToken('sometoken')->shouldBeCalledOnce();
        $adminUser->setPasswordRequestedAt($now)->shouldBeCalledOnce();

        $sendResetPasswordEmail = new SendResetPasswordEmail('admin@example.com', 'en_US');

        $messageBus
            ->dispatch(
                $sendResetPasswordEmail,
                [new DispatchAfterCurrentBusStamp()],
            )
            ->willReturn(new Envelope($sendResetPasswordEmail))
            ->shouldBeCalledOnce()
        ;

        $this(new RequestResetPasswordEmail('admin@example.com'));
    }

    public function it_does_nothing_while_handling_if_user_doesnt_exist(
        UserRepositoryInterface $userRepository,
        MessageBusInterface $messageBus,
    ): void {
        $userRepository->findOneByEmail('admin@example.com')->willReturn(null);

        $messageBus->dispatch(Argument::any())->shouldNotBeCalled();

        $this(new RequestResetPasswordEmail('admin@example.com'));
    }
}
