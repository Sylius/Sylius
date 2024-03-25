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

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

interface CreatePageInterface extends BaseCreatePageInterface
{
    public function isAvatarAttached(): bool;

    public function attachAvatar(string $path): void;

    public function enable(): void;

    public function specifyUsername(string $username): void;

    public function specifyEmail(string $email): void;

    public function specifyFirstName(string $firstName): void;

    public function specifyLastName(string $lastName): void;

    public function specifyPassword(string $password): void;

    public function specifyLocale(string $localeCode): void;
}
