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

namespace Sylius\Behat\Page\Admin\Account;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

class LoginPage extends SymfonyPage implements LoginPageInterface
{
    public function hasValidationErrorWith(string $message): bool
    {
        return $this->getElement('validation_error')->getText() === $message;
    }

    public function logIn(): void
    {
        $this->getDocument()->pressButton('Login');
    }

    public function specifyPassword(string $password): void
    {
        $this->getDocument()->fillField('Password', $password);
    }

    public function specifyUsername(string $username): void
    {
        $this->getDocument()->fillField('Username', $username);
    }

    public function getRouteName(): string
    {
        return 'sylius_admin_login';
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'validation_error' => '.message.negative',
        ]);
    }
}
