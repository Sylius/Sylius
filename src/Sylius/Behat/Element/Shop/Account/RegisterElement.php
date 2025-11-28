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
use Sylius\Behat\Context\Ui\Admin\Helper\SecurePasswordTrait;
use Sylius\Behat\Element\NodeElement;
use Sylius\Behat\Element\SyliusElement;
use Sylius\Behat\Service\SharedStorageInterface;

class RegisterElement extends SyliusElement implements RegisterElementInterface
{
    use SecurePasswordTrait;

    public function __construct(
        Session $session,
        $minkParameters = [],
        protected ?SharedStorageInterface $sharedStorage = null,
    ) {
        parent::__construct($session, $minkParameters);
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

    /**
     * @param array<string, string> $parameters
     */
    public function getValidationMessage(string $element, array $parameters = []): string
    {
        $foundElement = $this->getFieldElement($element, $parameters);

        $validationMessage = $foundElement->find('css', '.invalid-feedback');
        if (null === $validationMessage) {
            throw new ElementNotFoundException($this->getSession(), 'Validation message', 'css', '.invalid-feedback');
        }

        return $validationMessage->getText();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'email' => '[data-test-email]',
            'first_name' => '[data-test-first-name]',
            'form' => '[data-live-name-value="sylius_shop:account:register:form"]',
            'last_name' => '[data-test-last-name]',
            'newsletter' => '[data-test-subscribed-to-newsletter]',
            'password' => '[data-test-password-first]',
            'password_verification' => '[data-test-password-second]',
            'phone_number' => '[data-test-phone-number]',
            'register_button' => '[data-test-button="register-button"]',
        ]);
    }

    /**
     * @param array<string, string> $parameters
     *
     * @throws ElementNotFoundException
     */
    protected function getFieldElement(string $element, array $parameters): NodeElement
    {
        $element = $this->getElement($element, $parameters);
        while (null !== $element && !$element->hasClass('field')) {
            $element = $element->getParent();
        }

        return $element;
    }
}
