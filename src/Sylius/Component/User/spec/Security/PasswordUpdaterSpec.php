<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\User\Security;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Security\PasswordUpdater;
use Sylius\Component\User\Security\PasswordUpdaterInterface;
use Sylius\Component\User\Security\UserPasswordEncoderInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class PasswordUpdaterSpec extends ObjectBehavior
{
    function let(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->beConstructedWith($userPasswordEncoder);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PasswordUpdater::class);
    }

    function it_implements_password_updater_interface()
    {
        $this->shouldImplement(PasswordUpdaterInterface::class);
    }

    function it_updates_user_profile_with_encoded_password(UserPasswordEncoderInterface $userPasswordEncoder, UserInterface $user)
    {
        $user->getPlainPassword()->willReturn('topSecretPlainPassword');

        $userPasswordEncoder->encode($user)->willReturn('topSecretEncodedPassword');

        $user->eraseCredentials()->shouldBeCalled();
        $user->setPassword('topSecretEncodedPassword')->shouldBeCalled();

        $this->updatePassword($user);
    }

    function it_does_nothing_if_plain_password_is_empty(UserPasswordEncoderInterface $userPasswordEncoder, UserInterface $user)
    {
        $user->getPlainPassword()->willReturn('');

        $userPasswordEncoder->encode($user)->willReturn('topSecretEncodedPassword');

        $user->setPassword(Argument::any())->shouldNotBeCalled();
        $user->eraseCredentials()->shouldNotBeCalled();

        $this->updatePassword($user);
    }
}
