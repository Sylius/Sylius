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

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;

interface UpdatePageInterface extends BaseUpdatePageInterface
{
    public function attachAvatar(string $path): void;

    public function changeUsername(string $username): void;

    public function changeEmail(string $email): void;

    public function changePassword(string $password): void;

    public function changeLocale(string $localeCode): void;

    public function removeAvatar(): void;

    public function hasAvatar(string $avatarPath): bool;
}
