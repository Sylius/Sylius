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

class ProfileUpdatePage extends SymfonyPage implements ProfileUpdatePageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_shop_account_profile_update';
    }

    public function checkValidationMessageFor(string $element, string $message): bool
    {
        $errorLabel = $this->getElement($element)->getParent()->find('css', '[data-test-validation-error]');

        if (null === $errorLabel) {
            throw new ElementNotFoundException($this->getSession(), 'Validation message', 'css', '[data-test-validation-error]');
        }

        return $message === $errorLabel->getText();
    }

    public function specifyFirstName(?string $firstName): void
    {
        $this->getElement('first_name')->setValue($firstName);
    }

    public function specifyPhoneNumber(?string $phoneNumber): void
    {
        $this->getElement('phone_number')->setValue($phoneNumber);
    }

    public function getPhoneNumber(): string
    {
        return $this->getElement('phone_number')->getValue();
    }

    public function specifyLastName(?string $lastName): void
    {
        $this->getElement('last_name')->setValue($lastName);
    }

    public function specifyEmail(?string $email): void
    {
        $this->getElement('email')->setValue($email);
    }

    public function saveChanges(): void
    {
        $this->getElement('save_changes_button')->press();
    }

    public function subscribeToTheNewsletter(): void
    {
        $this->getElement('subscribe_newsletter')->check();
    }

    public function isSubscribedToTheNewsletter(): bool
    {
        return $this->getElement('subscribe_newsletter')->isChecked();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'email' => '[data-test-email]',
            'first_name' => '[data-test-first-name]',
            'last_name' => '[data-test-last-name]',
            'phone_number' => '#sylius_customer_profile_phoneNumber',
            'save_changes_button' => '[data-test-save-changes]',
            'subscribe_newsletter' => '[data-test-subscribe-newsletter]',
        ]);
    }
}
