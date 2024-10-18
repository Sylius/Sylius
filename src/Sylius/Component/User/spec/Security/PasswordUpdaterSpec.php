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

namespace spec\Sylius\Component\User\Security;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Security\PasswordUpdaterInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class PasswordUpdaterSpec extends ObjectBehavior
{
    function let(UserPasswordHasherInterface $userPasswordHasher): void
    {
        $this->beConstructedWith($userPasswordHasher);
    }

    function it_implements_password_updater_interface(): void
    {
        $this->shouldImplement(PasswordUpdaterInterface::class);
    }

    function it_updates_user_profile_with_hashed_password(
        UserPasswordHasherInterface $userPasswordHasher,
        UserInterface $user,
    ): void {
        $user->getPlainPassword()->willReturn('topSecretPlainPassword');
        $userPasswordHasher->hashPassword($user, 'topSecretPlainPassword')->willReturn('topSecretHashedPassword');

        $user->eraseCredentials()->shouldBeCalled();
        $user->setPassword('topSecretHashedPassword')->shouldBeCalled();

        $this->updatePassword($user);
    }

    function it_does_nothing_if_plain_password_is_empty(UserInterface $user): void
    {
        $user->getPlainPassword()->willReturn('');

        $this->updatePassword($user);

        $user->setPassword(Argument::any())->shouldNotBeCalled();
        $user->eraseCredentials()->shouldNotBeCalled();
    }

    function it_does_nothing_if_plain_password_is_null(UserInterface $user): void
    {
        $user->getPlainPassword()->willReturn(null);

        $this->updatePassword($user);

        $user->setPassword(Argument::any())->shouldNotBeCalled();
        $user->eraseCredentials()->shouldNotBeCalled();
    }
}
