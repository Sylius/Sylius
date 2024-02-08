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

namespace Sylius\Behat\Page\Shop\Contact;

use Behat\Mink\Exception\ElementNotFoundException;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

class ContactPage extends SymfonyPage implements ContactPageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_shop_contact_request';
    }

    public function specifyEmail(?string $email): void
    {
        $this->getDocument()->fillField('Email', $email);
    }

    public function specifyMessage(?string $message): void
    {
        $this->getDocument()->fillField('Message', $message);
    }

    public function send(): void
    {
        $this->getElement('send_button')->click();
    }

    /**
     * @throws ElementNotFoundException
     */
    public function getValidationMessageFor(string $element): string
    {
        $errorLabel = $this->getElement($element)->getParent()->find('css', '[data-test-validation-error]');

        if (null === $errorLabel) {
            throw new ElementNotFoundException(
                $this->getSession(),
                'Validation message',
                'css',
                '[data-test-validation-error]',
            )
            ;
        }

        return $errorLabel->getText();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'email' => '[data-test-contact-email]',
            'message' => '[data-test-contact-message]',
            'send_button' => '[data-test-send-button]',
        ]);
    }
}
