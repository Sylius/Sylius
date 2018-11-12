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

use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    /**
     * {@inheritdoc}
     */
    public function specifyFirstName(string $name): void
    {
        $this->getDocument()->fillField('First name', $name);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyLastName(string $name): void
    {
        $this->getDocument()->fillField('Last name', $name);
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
    public function specifyBirthday(string $birthday): void
    {
        $this->getDocument()->fillField('Birthday', $birthday);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyPassword(string $password): void
    {
        $this->getDocument()->fillField('Password', $password);
    }

    /**
     * {@inheritdoc}
     */
    public function chooseGender(string $gender): void
    {
        $this->getDocument()->selectFieldOption('Gender', $gender);
    }

    /**
     * {@inheritdoc}
     */
    public function chooseGroup(string $group): void
    {
        $this->getDocument()->selectFieldOption('Group', $group);
    }

    /**
     * {@inheritdoc}
     */
    public function selectCreateAccount(): void
    {
        $this->getDocument()->find('css', 'label[for=sylius_customer_createUser]')->click();
    }

    /**
     * {@inheritdoc}
     */
    public function hasPasswordField(): bool
    {
        return null !== $this->getDocument()->find('css', '#sylius_customer_user_plainPassword');
    }

    /**
     * {@inheritdoc}
     */
    public function hasCheckedCreateOption(): bool
    {
        return $this->getElement('create_customer_user')->hasAttribute('checked');
    }

    /**
     * {@inheritdoc}
     */
    public function hasCreateOption(): bool
    {
        return null !== $this->getDocument()->find('css', '#sylius_customer_createUser');
    }

    /**
     * {@inheritdoc}
     */
    public function isUserFormHidden(): bool
    {
        return false !== strpos($this->getElement('user_form')->getAttribute('style'), 'none');
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'create_customer_user' => '#sylius_customer_createUser',
            'email' => '#sylius_customer_email',
            'first_name' => '#sylius_customer_firstName',
            'last_name' => '#sylius_customer_lastName',
            'password' => '#sylius_customer_user_plainPassword',
            'user_form' => '#user-form',
        ]);
    }
}
