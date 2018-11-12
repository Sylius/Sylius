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

namespace Sylius\Behat\Page\Shop\Account;

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\SymfonyPage;

class ProfileUpdatePage extends SymfonyPage implements ProfileUpdatePageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRouteName(): string
    {
        return 'sylius_shop_account_profile_update';
    }

    /**
     * {@inheritdoc}
     */
    public function checkValidationMessageFor(string $element, string $message): bool
    {
        $errorLabel = $this->getElement($element)->getParent()->find('css', '.sylius-validation-error');

        if (null === $errorLabel) {
            throw new ElementNotFoundException($this->getSession(), 'Validation message', 'css', '.sylius-validation-error');
        }

        return $message === $errorLabel->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function specifyFirstName(string $firstName): void
    {
        $this->getDocument()->fillField('First name', $firstName);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyLastName(string $lastName): void
    {
        $this->getDocument()->fillField('Last name', $lastName);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyEmail(string $email): void
    {
        $this->getDocument()->fillField('Email', $email);
    }

    /**
     * {@inheritdoc}
     */
    public function saveChanges(): void
    {
        $this->getDocument()->pressButton('Save changes');
    }

    public function subscribeToTheNewsletter(): void
    {
        $this->getDocument()->checkField('Subscribe to the newsletter');
    }

    /**
     * {@inheritdoc}
     */
    public function isSubscribedToTheNewsletter(): bool
    {
        return $this->getDocument()->hasCheckedField('Subscribe to the newsletter');
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'email' => '#sylius_customer_profile_email',
            'first_name' => '#sylius_customer_profile_firstName',
            'last_name' => '#sylius_customer_profile_lastName',
        ]);
    }
}
