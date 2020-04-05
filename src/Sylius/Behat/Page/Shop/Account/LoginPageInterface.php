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

namespace Sylius\Behat\Page\Shop\Account;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;

interface LoginPageInterface extends SymfonyPageInterface
{
    public function hasValidationErrorWith(string $message): bool;

    public function logIn(): void;

    public function specifyPassword(string $password): void;

    public function specifyUsername(string $username): void;
}
