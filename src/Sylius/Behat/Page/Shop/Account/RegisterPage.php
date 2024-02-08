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
use Sylius\Component\Core\Formatter\StringInflector;

class RegisterPage extends SymfonyPage implements RegisterPageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_shop_register';
    }

    /**
     * @throws ElementNotFoundException
     */
    public function checkValidationMessageFor(string $element, string $message): bool
    {
        $errorLabel = $this
            ->getElement(StringInflector::nameToCode($element))
            ->getParent()
            ->find('css', '[data-test-validation-error]')
        ;

        if (null === $errorLabel) {
            throw new ElementNotFoundException($this->getSession(), 'Validation message', 'css', '[data-test-validation-error]');
        }

        return $message === $errorLabel->getText();
    }

    public function register(): void
    {
        $this->getElement('create_account_button')->press();
    }

    public function specifyEmail(string $email): void
    {
        $this->getElement('email')->setValue($email);
    }

    public function specifyFirstName(string $firstName): void
    {
        $this->getElement('first_name')->setValue($firstName);
    }

    public function specifyLastName(string $lastName): void
    {
        $this->getElement('last_name')->setValue($lastName);
    }

    public function specifyPassword(string $password): void
    {
        $this->getElement('password')->setValue($password);
    }

    public function specifyPhoneNumber(string $phoneNumber): void
    {
        $this->getElement('phone_number')->setValue($phoneNumber);
    }

    public function verifyPassword(string $password): void
    {
        $this->getElement('password_verification')->setValue($password);
    }

    public function subscribeToTheNewsletter(): void
    {
        $this->getElement('subscribe_newsletter')->check();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'create_account_button' => '[data-test-register-button]',
            'email' => '[data-test-email]',
            'first_name' => '[data-test-first-name]',
            'last_name' => '[data-test-last-name]',
            'password' => '[data-test-password-first]',
            'password_verification' => '[data-test-password-second]',
            'phone_number' => '[data-test-phone-number]',
            'subscribe_newsletter' => '[data-test-subscribed-to-newsletter]',
        ]);
    }
}
