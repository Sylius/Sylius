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

use Behat\Mink\Exception\ElementNotFoundException;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

class ResetPasswordPage extends SymfonyPage implements ResetPasswordPageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_shop_password_reset';
    }

    public function reset(): void
    {
        $this->getDocument()->pressButton('Reset');
    }

    public function specifyNewPassword(string $password): void
    {
        $this->getElement('password')->setValue($password);
    }

    public function specifyConfirmPassword(string $password): void
    {
        $this->getElement('confirm_password')->setValue($password);
    }

    public function checkValidationMessageFor(string $element, string $message): bool
    {
        $errorLabel = $this->getElement($element)->getParent()->find('css', '[data-test-validation-error]');

        if (null === $errorLabel) {
            throw new ElementNotFoundException($this->getSession(), 'Validation message', 'css', '[data-test-validation-error]');
        }

        return $message === $errorLabel->getText();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'password' => '[data-test-password-reset-new]',
            'confirm_password' => '[data-test-password-reset-confirmation]',
        ]);
    }
}
