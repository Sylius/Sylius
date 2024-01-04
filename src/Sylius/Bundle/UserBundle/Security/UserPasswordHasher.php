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

namespace Sylius\Bundle\UserBundle\Security;

use Sylius\Component\User\Model\CredentialsHolderInterface;
use Sylius\Component\User\Security\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Security\Core\User\LegacyPasswordAuthenticatedUserInterface;

final class UserPasswordHasher implements UserPasswordHasherInterface
{
    public function __construct(private PasswordHasherFactoryInterface $passwordHasherFactory)
    {
    }

    /**
     * @param CredentialsHolderInterface&LegacyPasswordAuthenticatedUserInterface $user
     */
    public function hash(CredentialsHolderInterface $user): string
    {
        $passwordHasher = $this->passwordHasherFactory->getPasswordHasher($user::class);

        /**
         * @phpstan-ignore-next-line
         * Method hash on PasswordHasherInterface has only one parameter, here we are calling with two for some
         * LegacyPasswordHasherInterface. Anyway this error can be suppressed as in PHP it is not considered as an error
         * pass more parameters than expecting (LegacyPasswordHasherInterface)
         */
        return $passwordHasher->hash($user->getPlainPassword(), $user->getSalt());
    }
}
