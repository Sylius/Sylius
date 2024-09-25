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
use Sylius\Component\User\Model\CredentialsHolderInterface;
use Sylius\Component\User\Security\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

final class UserPasswordHasherSpec extends ObjectBehavior
{
    function let(PasswordHasherFactoryInterface $passwordHasherFactory): void
    {
        $this->beConstructedWith($passwordHasherFactory);
    }

    function it_implements_user_password_hasher_interface(): void
    {
        $this->shouldImplement(UserPasswordHasherInterface::class);
    }

    function it_hashes_password(
        PasswordHasherFactoryInterface $passwordHasherFactory,
        PasswordHasherInterface $passwordHasher,
        CredentialsHolderInterface $user,
    ): void {
        $user->getPlainPassword()->willReturn('topSecretPlainPassword');
        $passwordHasherFactory->getPasswordHasher($user->getWrappedObject()::class)->willReturn($passwordHasher);
        $passwordHasher->hash('topSecretPlainPassword')->willReturn('topSecretHashedPassword');

        $this->hash($user)->shouldReturn('topSecretHashedPassword');
    }
}
