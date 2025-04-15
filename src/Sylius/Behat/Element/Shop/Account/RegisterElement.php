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

namespace Sylius\Behat\Element\Shop\Account;

use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Session;
use FriendsOfBehat\PageObjectExtension\Element\Element;
use Sylius\Behat\Context\Ui\Admin\Helper\SecurePasswordTrait;
use Sylius\Behat\Service\DriverHelper;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Formatter\StringInflector;

final class RegisterElement extends Element implements RegisterElementInterface
{
    use SecurePasswordTrait;

    public function __construct(
        Session $session,
        $minkParameters = [],
        protected ?SharedStorageInterface $sharedStorage = null,
    )
    {
        parent::__construct($session, $minkParameters);
    }

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
        $this->getElement('register_button')->click();
    }

    public function specifyEmail(?string $email): void
    {
        $this->getElement('email')->setValue($email);
    }

    public function getEmail(): string
    {
        return $this->getElement('email')->getValue();
    }

    public function specifyFirstName(?string $firstName): void
    {
        $this->getElement('first_name')->setValue($firstName);
    }

    public function specifyLastName(?string $lastName): void
    {
        $this->getElement('last_name')->setValue($lastName);
    }

    public function specifyPassword(string $password): void
    {
        $this->getElement('password')->setValue($this->replaceWithSecurePassword($password));
    }

    public function specifyPhoneNumber(string $phoneNumber): void
    {
        $this->getElement('phone_number')->setValue($phoneNumber);
    }

    public function verifyPassword(string $password): void
    {
        $this->getElement('password_verification')->setValue($this->confirmSecurePassword($password));
    }

    public function subscribeToTheNewsletter(): void
    {
        $this->getElement('newsletter')->check();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'email' => '[data-test-email]',
            'first_name' => '[data-test-first-name]',
            'last_name' => '[data-test-last-name]',
            'newsletter' => '[data-test-subscribed-to-newsletter]',
            'password' => '[data-test-password-first]',
            'password_verification' => '[data-test-password-second]',
            'phone_number' => '[data-test-phone-number]',
            'register_button' => '[data-test-register-button]',
        ]);
    }
}
