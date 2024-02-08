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

interface ResetPasswordPageInterface extends SymfonyPageInterface
{
    public function reset(): void;

    public function specifyNewPassword(string $password): void;

    public function specifyConfirmPassword(string $password): void;

    public function checkValidationMessageFor(string $element, string $message): bool;
}
