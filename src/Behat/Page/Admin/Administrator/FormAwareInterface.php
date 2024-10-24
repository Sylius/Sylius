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

namespace Sylius\Behat\Page\Admin\Administrator;

interface FormAwareInterface
{
    public function setFirstName(string $firstName): void;

    public function getFirstName(): string;

    public function setLastName(string $lastName): void;

    public function getLastName(): string;

    public function setUsername(string $username): void;

    public function getUsername(): string;

    public function setEmail(string $email): void;

    public function getEmail(): string;

    public function setPassword(string $password): void;

    public function getPassword(): string;

    public function setLocale(string $locale): void;

    public function getLocale(): string;

    public function enable(): void;

    public function disable(): void;

    public function isEnabled(): bool;

    public function attachAvatar(string $path): void;

    public function isAvatarAttached(): bool;
}
