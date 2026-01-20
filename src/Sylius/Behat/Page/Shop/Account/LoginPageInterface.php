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

namespace Sylius\Behat\Page\Shop\Account;

use Sylius\Behat\Page\SyliusPageInterface;

interface LoginPageInterface extends SyliusPageInterface
{
    public function hasValidationErrorWith(string $message): bool;

    public function logIn(): void;

    public function specifyPassword(string $password): void;

    public function specifyUsername(string $username): void;
}
