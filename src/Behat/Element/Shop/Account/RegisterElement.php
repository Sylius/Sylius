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

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use FriendsOfBehat\PageObjectExtension\Element\Element;

final class RegisterElement extends Element implements RegisterElementInterface
{
    public function register(): void
    {
        $this->getElement('register_button')->click();
    }

    public function specifyEmail(?string $email): void
    {
        $this->getElement('email')->setValue($email);
        $this->waitForFormUpdate();
    }

    public function getEmail(): string
    {
        return $this->getElement('email')->getValue();
    }

    public function specifyFirstName(?string $firstName): void
    {
        $this->getElement('first_name')->setValue($firstName);
        $this->waitForFormUpdate();
    }

    public function specifyLastName(?string $lastName): void
    {
        $this->getElement('last_name')->setValue($lastName);
        $this->waitForFormUpdate();
    }

    public function specifyPassword(?string $password): void
    {
        $this->getElement('password')->setValue($password);
        $this->waitForFormUpdate();
    }

    public function specifyPhoneNumber(string $phoneNumber): void
    {
        $this->getElement('phone_number')->setValue($phoneNumber);
        $this->waitForFormUpdate();
    }

    public function verifyPassword(?string $password): void
    {
        $this->getElement('password_verification')->setValue($password);
        $this->waitForFormUpdate();
    }

    public function subscribeToTheNewsletter(): void
    {
        $this->getElement('newsletter')->check();
        $this->waitForFormUpdate();
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

    protected function waitForFormUpdate(): void
    {
        $form = $this->getElement('form');

        usleep(500000); // we need to sleep, as sometimes the check below is executed faster than the form sets the busy attribute
        $form->waitFor(1500, fn () => !$form->hasAttribute('busy'));
    }

    /**
     * @param array<string, string> $parameters
     *
     * @throws ElementNotFoundException
     */
    private function getFieldElement(string $element, array $parameters): NodeElement
    {
        $element = $this->getElement($element, $parameters);
        while (null !== $element && !$element->hasClass('field')) {
            $element = $element->getParent();
        }

        return $element;
    }
}
