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
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

/**
* @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
*/
class PasswordUpdaterSpec extends ObjectBehavior
{
    public function let(EncoderFactoryInterface $encoderFactory)
    {
        $this->beConstructedWith($encoderFactory);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\User\Security\PasswordUpdater');
    }

    public function it_implements_password_updater_interface()
    {
        $this->shouldImplement('Sylius\Component\User\Security\PasswordUpdaterInterface');
    }

    public function it_updates_user_profile_with_encoded_password($encoderFactory, PasswordEncoderInterface $encoder, UserInterface $user)
    {
        $user->getPlainPassword()->willReturn('topSecretPlainPassword');
        $user->getSalt()->willReturn('typicalSalt');

        $encoderFactory->getEncoder($user)->willReturn($encoder);
        $encoder->encodePassword('topSecretPlainPassword', 'typicalSalt')->willReturn('topSecretEncodedPassword');

        $user->eraseCredentials()->shouldBeCalled();
        $user->setPassword('topSecretEncodedPassword')->shouldBeCalled();

        $this->updatePassword($user);
    }

    public function it_does_nothing_if_plain_password_is_empty($encoderFactory, PasswordEncoderInterface $encoder, UserInterface $user)
    {
        $user->getPlainPassword()->willReturn('');
        $user->getSalt()->willReturn('typicalSalt');

        $encoderFactory->getEncoder($user)->shouldNotBeCalled();
        $encoder->encodePassword('', 'typicalSalt')->shouldNotBeCalled();

        $user->setPassword(Argument::any())->shouldNotBeCalled();
        $user->eraseCredentials()->shouldNotBeCalled();

        $this->updatePassword($user);
    }
}
