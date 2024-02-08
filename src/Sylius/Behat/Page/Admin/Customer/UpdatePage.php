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

namespace Sylius\Behat\Page\Admin\Customer;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Behaviour\Toggles;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use Toggles;

    public function getFullName(): string
    {
        $firstNameElement = $this->getElement('first_name')->getValue();
        $lastNameElement = $this->getElement('last_name')->getValue();

        return sprintf('%s %s', $firstNameElement, $lastNameElement);
    }

    public function changeFirstName(string $firstName): void
    {
        $this->getDocument()->fillField('First name', $firstName);
    }

    public function getFirstName(): string
    {
        return $this->getElement('first_name')->getValue();
    }

    public function changeLastName(string $lastName): void
    {
        $this->getDocument()->fillField('Last name', $lastName);
    }

    public function getLastName(): string
    {
        return $this->getElement('last_name')->getValue();
    }

    public function changeEmail(string $email): void
    {
        $this->getDocument()->fillField('Email', $email);
    }

    public function changePassword(string $password): void
    {
        $this->getDocument()->fillField('Password', $password);
    }

    public function getPassword(): string
    {
        return $this->getElement('password')->getValue();
    }

    public function subscribeToTheNewsletter(): void
    {
        $this->getDocument()->checkField('Subscribe to the newsletter');
    }

    public function isSubscribedToTheNewsletter(): bool
    {
        return $this->getDocument()->hasCheckedField('Subscribe to the newsletter');
    }

    public function getGroupName(): string
    {
        return $this->getElement('group')->getText();
    }

    public function verifyUser(): void
    {
        $this->getDocument()->checkField('Verified');
    }

    protected function getToggleableElement(): NodeElement
    {
        return $this->getElement('enabled');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'email' => '#sylius_customer_email',
            'enabled' => '#sylius_customer_user_enabled',
            'first_name' => '#sylius_customer_firstName',
            'group' => '#sylius_customer_group',
            'last_name' => '#sylius_customer_lastName',
            'password' => '#sylius_customer_user_password',
            'verified_at' => '#sylius_customer_user_verifiedAt',
        ]);
    }
}
