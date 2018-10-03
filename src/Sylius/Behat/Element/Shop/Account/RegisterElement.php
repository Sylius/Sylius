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

namespace Sylius\Behat\Element\Shop\Account;

use Behat\Mink\Exception\ElementNotFoundException;
use FriendsOfBehat\PageObjectExtension\Element\Element;
use Sylius\Component\Core\Formatter\StringInflector;

final class RegisterElement extends Element implements RegisterElementInterface
{
    public function checkValidationMessageFor(string $element, string $message): bool
    {
        $errorLabel = $this
            ->getElement(StringInflector::nameToCode($element))
            ->getParent()
            ->find('css', '.sylius-validation-error')
        ;

        if (null === $errorLabel) {
            throw new ElementNotFoundException($this->getSession(), 'Validation message', 'css', '.sylius-validation-error');
        }

        return $message === $errorLabel->getText();
    }

    public function register(): void
    {
        $this->getDocument()->pressButton('Create an account');
    }

    public function specifyEmail(?string $email): void
    {
        $this->getDocument()->fillField('Email', $email);
    }

    public function getEmail(): string
    {
        return $this->getElement('email')->getValue();
    }

    public function specifyFirstName(?string $firstName): void
    {
        $this->getDocument()->fillField('First name', $firstName);
    }

    public function specifyLastName(?string $lastName): void
    {
        $this->getDocument()->fillField('Last name', $lastName);
    }

    public function specifyPassword(?string $password): void
    {
        $this->getDocument()->fillField('Password', $password);
    }

    public function specifyPhoneNumber(string $phoneNumber): void
    {
        $this->getDocument()->fillField('Phone number', $phoneNumber);
    }

    public function verifyPassword(?string $password): void
    {
        $this->getDocument()->fillField('Verification', $password);
    }

    public function subscribeToTheNewsletter(): void
    {
        $this->getDocument()->checkField('Subscribe to the newsletter');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'email' => '#sylius_customer_registration_email',
            'first_name' => '#sylius_customer_registration_firstName',
            'last_name' => '#sylius_customer_registration_lastName',
            'password' => '#sylius_customer_registration_user_plainPassword_first',
            'password_verification' => '#sylius_customer_registration_user_plainPassword_second',
            'phone_number' => '#sylius_customer_registration_phoneNumber',
        ]);
    }
}
