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

namespace spec\Sylius\Bundle\UserBundle\Security;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\User\Model\CredentialsHolderInterface;
use Sylius\Component\User\Security\UserPasswordEncoderInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

if (Kernel::MAJOR_VERSION === 5) {
    final class UserPasswordEncoderSpec extends ObjectBehavior
    {
        function it_implements_password_encoder_interface(EncoderFactoryInterface $encoderFactory): void
        {
            $this->beConstructedWith($encoderFactory);
            $this->shouldImplement(UserPasswordEncoderInterface::class);
        }

        function it_encodes_password(
            EncoderFactoryInterface $encoderFactory,
            PasswordEncoderInterface $passwordEncoder,
            CredentialsHolderInterface $user,
        ): void {
            $this->beConstructedWith($encoderFactory);
            $encoderFactory->getEncoder($user->getWrappedObject())->willReturn($passwordEncoder);

            $user->getPlainPassword()->willReturn('topSecretPlainPassword');
            $user->getSalt()->willReturn('typicalSalt');

            $passwordEncoder
                ->encodePassword('topSecretPlainPassword', 'typicalSalt')
                ->willReturn('topSecretEncodedPassword')
            ;

            $this->encode($user)->shouldReturn('topSecretEncodedPassword');
        }

        function it_throws_an_exception_during_instantiation_if_constructed_with_password_hasher_factory(
            PasswordHasherFactoryInterface $passwordHasherFactory,
            PasswordHasherInterface $passwordHasher,
            CredentialsHolderInterface $user,
        ): void {
            $this->beConstructedWith($passwordHasherFactory);
            $passwordHasherFactory->getPasswordHasher($user->getWrappedObject())->shouldNotBeCalled();

            $user->getPlainPassword()->shouldNotBeCalled();
            $user->getSalt()->shouldNotBeCalled();

            $passwordHasher->hash(Argument::any())->shouldNotBeCalled();

            $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
        }
    }
}

if (Kernel::MAJOR_VERSION === 6) {
    final class UserPasswordEncoderSpec extends ObjectBehavior
    {
        function let(PasswordHasherFactoryInterface $passwordHasherFactory): void
        {
            $this->beConstructedWith($passwordHasherFactory);
        }

        function it_throws_an_exception_during_instantiation(
            PasswordHasherFactoryInterface $passwordHasherFactory,
            PasswordHasherInterface $passwordHasher,
            CredentialsHolderInterface $user,
        ): void {
            $passwordHasherFactory->getPasswordHasher($user->getWrappedObject())->shouldNotBeCalled();

            $user->getPlainPassword()->shouldNotBeCalled();
            $user->getSalt()->shouldNotBeCalled();

            $passwordHasher->hash(Argument::any())->shouldNotBeCalled();

            $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
        }
    }
}
