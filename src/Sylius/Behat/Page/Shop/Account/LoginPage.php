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

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

class LoginPage extends SymfonyPage implements LoginPageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_shop_login';
    }

    public function hasValidationErrorWith(string $message): bool
    {
        return $this->getElement('validation_error')->getText() === $message;
    }

    public function logIn(): void
    {
        $this->getElement('login_button')->click();
    }

    public function specifyPassword(string $password): void
    {
        $this->getElement('password')->setValue($password);
    }

    public function specifyUsername(string $username): void
    {
        $this->getElement('username')->setValue($username);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'login_button' => '[data-test-login-button]',
            'password' => '[data-test-login-password]',
            'username' => '[data-test-login-username]',
            'validation_error' => '[data-test-flash-message="negative"]',
        ]);
    }
}
