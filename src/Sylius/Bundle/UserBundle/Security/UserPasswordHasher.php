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

namespace Sylius\Bundle\UserBundle\Security;

use Sylius\Component\User\Model\CredentialsHolderInterface;
use Sylius\Component\User\Security\UserPasswordEncoderInterface;
use Sylius\Component\User\Security\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

final class UserPasswordHasher implements UserPasswordEncoderInterface
{
    public function __construct(private PasswordHasherFactoryInterface $passwordHasherFactory)
    {
    }

    public function encode(CredentialsHolderInterface $user): string
    {
        return $this->hash($user);
    }

    public function hash(CredentialsHolderInterface $user): string
    {
        /** @psalm-suppress InvalidArgument */
        $passwordHasher = $this->passwordHasherFactory->getPasswordHasher($user);

        return $passwordHasher->hash($user->getPlainPassword(), $user->getSalt());
    }
}
