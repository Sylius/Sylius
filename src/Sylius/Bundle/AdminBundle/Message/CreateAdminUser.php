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

namespace Sylius\Bundle\AdminBundle\Message;

final class CreateAdminUser
{
    public function __construct(
        private string $email,
        private string $username,
        private ?string $firstName,
        private ?string $lastName,
        private string $plainPassword,
        private string $localeCode,
        private bool $enabled,
    ) {
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPlainPassword(): string
    {
        return $this->plainPassword;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getLocaleCode(): string
    {
        return $this->localeCode;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
