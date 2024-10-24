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

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;

interface RegisterPageInterface extends SymfonyPageInterface
{
    public function getRouteName(): string;

    public function checkValidationMessageFor(string $element, string $message): bool;

    public function register(): void;

    public function specifyEmail(string $email): void;

    public function specifyFirstName(string $firstName): void;

    public function specifyPassword(string $password): void;

    public function specifyPhoneNumber(string $phoneNumber): void;

    public function verifyPassword(string $password): void;

    public function subscribeToTheNewsletter(): void;
}
