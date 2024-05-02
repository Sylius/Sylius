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

namespace Sylius\Component\User\Model;

use Symfony\Component\Security\Core\User\LegacyPasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

// Symfony 5.4
if (\method_exists(SymfonyUserInterface::class, 'getPassword')) {
    interface CredentialsHolderInterface
    {
        public function getPlainPassword(): ?string;

        public function setPlainPassword(?string $plainPassword): void;

        /**
         * Returns the password used to authenticate the user.
         *
         * This should be the encoded password. On authentication, a plain-text
         * password will be salted, encoded, and then compared to this value.
         *
         * @return string|null
         */
        public function getPassword();

        public function setPassword(?string $encodedPassword): void;

        /**
         * Returns the salt that was originally used to encode the password.
         *
         * This can return null if the password was not encoded using a salt.
         *
         * @return string|null
         */
        public function getSalt();

        /**
         * Removes sensitive data from the user.
         *
         * This is important if, at any given point, sensitive information like
         * the plain-text password is stored on this object.
         */
        public function eraseCredentials();
    }
    // Symfony 6
} else {
    interface CredentialsHolderInterface extends LegacyPasswordAuthenticatedUserInterface
    {
        public function getPlainPassword(): ?string;

        public function setPlainPassword(?string $plainPassword): void;

        public function setPassword(?string $encodedPassword): void;

        /**
         * Removes sensitive data from the user.
         *
         * This is important if, at any given point, sensitive information like
         * the plain-text password is stored on this object.
         */
        public function eraseCredentials();
    }
}
