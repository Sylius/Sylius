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

namespace spec\Sylius\Bundle\CoreBundle\Security;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Security\UserPasswordResetterInterface;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\PasswordUpdaterInterface;

final class UserPasswordResetterSpec extends ObjectBehavior
{
    function let(UserRepositoryInterface $userRepository, PasswordUpdaterInterface $passwordUpdater)
    {
        $this->beConstructedWith($userRepository, $passwordUpdater, 'P5D');
    }

    function it_implements_user_password_resetter_interface(): void
    {
        $this->shouldImplement(UserPasswordResetterInterface::class);
    }

    function it_resets_password(
        UserRepositoryInterface $userRepository,
        UserInterface $user,
        PasswordUpdaterInterface $passwordUpdater,
    ): void {
        $userRepository->findOneBy(['passwordResetToken' => 'TOKEN'])->willReturn($user);

        $user->isPasswordRequestNonExpired(
            Argument::that(static fn (\DateInterval $dateInterval) => $dateInterval->format('%d') === '5'),
        )->willReturn(true);

        $user->getPasswordResetToken()->willReturn('TOKEN');

        $user->setPlainPassword('newPassword')->shouldBeCalled();
        $passwordUpdater->updatePassword($user)->shouldBeCalled();
        $user->setPasswordResetToken(null)->shouldBeCalled();
        $user->setPasswordRequestedAt(null)->shouldBeCalled();

        $this->reset('TOKEN', 'newPassword');
    }

    function it_throws_exception_if_no_user_has_been_found_for_token(
        UserRepositoryInterface $userRepository,
        PasswordUpdaterInterface $passwordUpdater,
    ): void {
        $userRepository->findOneBy(['passwordResetToken' => 'TOKEN'])->willReturn(null);

        $passwordUpdater->updatePassword(Argument::any())->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('reset', ['TOKEN', 'newPassword'])
        ;
    }

    function it_throws_exception_if_token_is_expired(
        UserRepositoryInterface $userRepository,
        UserInterface $user,
        PasswordUpdaterInterface $passwordUpdater,
    ): void {
        $userRepository->findOneBy(['passwordResetToken' => 'TOKEN'])->willReturn($user);

        $user->isPasswordRequestNonExpired(
            Argument::that(static fn (\DateInterval $dateInterval) => $dateInterval->format('%d') === '5'),
        )->willReturn(false);

        $user->getPasswordResetToken()->willReturn('TOKEN');
        $user->setPlainPassword('newPassword')->shouldNotBeCalled();
        $passwordUpdater->updatePassword($user)->shouldNotBeCalled();
        $user->setPasswordRequestedAt(null)->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('reset', ['TOKEN', 'newPassword'])
        ;
    }
}
