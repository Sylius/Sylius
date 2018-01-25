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

namespace spec\Sylius\Bundle\UserBundle\Security;

use PhpSpec\ObjectBehavior;
use Sylius\Component\User\Model\CredentialsHolderInterface;
use Sylius\Component\User\Security\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

final class UserPasswordEncoderSpec extends ObjectBehavior
{
    function let(EncoderFactoryInterface $encoderFactory): void
    {
        $this->beConstructedWith($encoderFactory);
    }

    function it_implements_password_updater_interface(): void
    {
        $this->shouldImplement(UserPasswordEncoderInterface::class);
    }

    function it_encodes_password(
        EncoderFactoryInterface $encoderFactory,
        PasswordEncoderInterface $passwordEncoder,
        CredentialsHolderInterface $user
    ): void {
        $user->getPlainPassword()->willReturn('topSecretPlainPassword');
        $user->getSalt()->willReturn('typicalSalt');
        $encoderFactory->getEncoder(get_class($user->getWrappedObject()))->willReturn($passwordEncoder);
        $passwordEncoder->encodePassword('topSecretPlainPassword', 'typicalSalt')->willReturn('topSecretEncodedPassword');

        $this->encode($user)->shouldReturn('topSecretEncodedPassword');
    }
}
