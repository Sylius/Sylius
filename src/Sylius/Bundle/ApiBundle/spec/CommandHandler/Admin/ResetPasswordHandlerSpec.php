<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ApiBundle\CommandHandler\Admin;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Command\Admin\ResetPassword;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\PasswordUpdaterInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ResetPasswordHandlerSpec extends ObjectBehavior
{
    function let(
        UserRepositoryInterface $userRepository,
        PasswordUpdaterInterface $passwordUpdater
    ): void {
        $this->beConstructedWith($userRepository, $passwordUpdater, 'P5D');
    }

    function it_is_a_message_handler(): void
    {
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    function it_resets_password(
        UserRepositoryInterface $userRepository,
        AdminUserInterface $adminUser,
        PasswordUpdaterInterface $passwordUpdater
    ): void {
        $userRepository->findOneBy(['passwordResetToken' => 'TOKEN'])->willReturn($adminUser);

        $adminUser->isPasswordRequestNonExpired(
            Argument::that(fn (\DateInterval $dateInterval) => $dateInterval->format('%d') === '5'
        ))->willReturn(true);

        $adminUser->getPasswordResetToken()->willReturn('TOKEN');

        $adminUser->setPlainPassword('newPassword')->shouldBeCalled();
        $passwordUpdater->updatePassword($adminUser)->shouldBeCalled();
        $adminUser->setPasswordResetToken(null)->shouldBeCalled();
        $adminUser->setPasswordRequestedAt(null)->shouldBeCalled();

        $command = new ResetPassword('TOKEN');
        $command->newPassword = 'newPassword';
        $command->resetPasswordToken = 'TOKEN';

        $this->__invoke($command);
    }

    function it_throws_exception_if_token_is_expired(
        UserRepositoryInterface $userRepository,
        AdminUserInterface $adminUser,
        PasswordUpdaterInterface $passwordUpdater
    ): void {
        $userRepository->findOneBy(['passwordResetToken' => 'TOKEN'])->willReturn($adminUser);

        $adminUser->isPasswordRequestNonExpired(
            Argument::that(fn (\DateInterval $dateInterval) => $dateInterval->format('%d') === '5'
        ))->willReturn(false);

        $adminUser->getPasswordResetToken()->willReturn('TOKEN');
        $adminUser->setPlainPassword('newPassword')->shouldNotBeCalled();
        $passwordUpdater->updatePassword($adminUser)->shouldNotBeCalled();
        $adminUser->setPasswordRequestedAt(null)->shouldNotBeCalled();

        $command = new ResetPassword('TOKEN');
        $command->newPassword = 'newPassword';
        $command->resetPasswordToken = 'TOKEN';

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [$command])
        ;
    }

    function it_throws_exception_if_tokens_are_not_exact(
        UserRepositoryInterface $userRepository,
        AdminUserInterface $adminUser,
        PasswordUpdaterInterface $passwordUpdater
    ): void {
        $userRepository->findOneBy(['passwordResetToken' => 'TOKEN'])->willReturn($adminUser);

        $adminUser->isPasswordRequestNonExpired(
            Argument::that(fn (\DateInterval $dateInterval) => $dateInterval->format('%d') === '5'
        ))->willReturn(false);

        $adminUser->getPasswordResetToken()->willReturn('BADTOKEN');
        $adminUser->setPlainPassword('newPassword')->shouldNotBeCalled();
        $passwordUpdater->updatePassword($adminUser)->shouldNotBeCalled();
        $adminUser->setPasswordRequestedAt(null)->shouldNotBeCalled();

        $command = new ResetPassword('TOKEN');
        $command->newPassword = 'newPassword';
        $command->resetPasswordToken = 'TOKEN';

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [$command])
        ;
    }
}
