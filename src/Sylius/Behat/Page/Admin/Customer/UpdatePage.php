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

namespace Sylius\Behat\Page\Admin\Customer;

use Sylius\Behat\Behaviour\Toggles;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use Toggles;

    /**
     * {@inheritdoc}
     */
    public function getFullName(): string
    {
        $firstNameElement = $this->getElement('first_name')->getValue();
        $lastNameElement = $this->getElement('last_name')->getValue();

        return sprintf('%s %s', $firstNameElement, $lastNameElement);
    }

    /**
     * {@inheritdoc}
     */
    public function changeFirstName(string $firstName): void
    {
        $this->getDocument()->fillField('First name', $firstName);
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstName(): string
    {
        return $this->getElement('first_name')->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function changeLastName(string $lastName): void
    {
        $this->getDocument()->fillField('Last name', $lastName);
    }

    /**
     * {@inheritdoc}
     */
    public function getLastName(): string
    {
        return $this->getElement('last_name')->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function changeEmail(string $email): void
    {
        $this->getDocument()->fillField('Email', $email);
    }

    /**
     * {@inheritdoc}
     */
    public function changePassword(string $password): void
    {
        $this->getDocument()->fillField('Password', $password);
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword(): string
    {
        return $this->getElement('password');
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
    public function getGroupName(): string
    {
        return $this->getElement('group')->getText();
    }

    /**
     * {@inheritdoc}
     */
    protected function getToggleableElement()
    {
        return $this->getElement('enabled');
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'email' => '#sylius_customer_email',
            'enabled' => '#sylius_customer_user_enabled',
            'first_name' => '#sylius_customer_firstName',
            'group' => '#sylius_customer_group',
            'last_name' => '#sylius_customer_lastName',
            'password' => '#sylius_customer_user_password',
        ]);
    }
}
