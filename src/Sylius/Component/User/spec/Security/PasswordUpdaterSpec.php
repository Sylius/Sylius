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
use Sylius\Component\User\Security\UserPasswordEncoderInterface;
use Sylius\Component\User\Security\UserPasswordHasherInterface;

final class PasswordUpdaterSpec extends ObjectBehavior
{
    function it_implements_password_updater_interface(UserPasswordHasherInterface $userPasswordHasher): void
    {
        $this->beConstructedWith($userPasswordHasher);
        $this->shouldImplement(PasswordUpdaterInterface::class);
    }

    function it_updates_user_profile_with_hashed_password_if_using_symfony_6(
        UserPasswordHasherInterface $userPasswordHasher,
        UserInterface $user,
    ): void {
        $this->beConstructedWith($userPasswordHasher);
        $user->getPlainPassword()->willReturn('topSecretPlainPassword');

        $userPasswordHasher->hash($user)->willReturn('topSecretHashedPassword');

        $user->eraseCredentials()->shouldBeCalled();
        $user->setPassword('topSecretHashedPassword')->shouldBeCalled();

        $this->updatePassword($user);
    }

    function it_updates_user_profile_with_encoded_password_if_using_symfony_5_4(
        UserPasswordEncoderInterface $userPasswordEncoder,
        UserInterface $user,
    ): void {
        $this->beConstructedWith($userPasswordEncoder);
        $user->getPlainPassword()->willReturn('topSecretPlainPassword');

        $userPasswordEncoder->encode($user)->willReturn('topSecretEncodedPassword');

        $user->eraseCredentials()->shouldBeCalled();
        $user->setPassword('topSecretEncodedPassword')->shouldBeCalled();

        $this->updatePassword($user);
    }

    function it_does_nothing_if_plain_password_is_empty(
        UserPasswordHasherInterface $userPasswordHasher,
        UserInterface $user,
    ): void {
        $this->beConstructedWith($userPasswordHasher);
        $user->getPlainPassword()->willReturn('');

        $userPasswordHasher->hash($user)->shouldNotBeCalled();

        $user->setPassword(Argument::any())->shouldNotBeCalled();
        $user->eraseCredentials()->shouldNotBeCalled();

        $this->updatePassword($user);
    }

    function it_does_nothing_if_plain_password_is_null(
        UserPasswordHasherInterface $userPasswordHasher,
        UserInterface $user,
    ): void {
        $this->beConstructedWith($userPasswordHasher);
        $user->getPlainPassword()->willReturn(null);

        $userPasswordHasher->hash($user)->shouldNotBeCalled();

        $user->setPassword(Argument::any())->shouldNotBeCalled();
        $user->eraseCredentials()->shouldNotBeCalled();

        $this->updatePassword($user);
    }
}
