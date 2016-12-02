<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Customer;

use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    /**
     * {@inheritdoc}
     */
    public function specifyFirstName($name)
    {
        $this->getDocument()->fillField('First name', $name);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyLastName($name)
    {
        $this->getDocument()->fillField('Last name', $name);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyEmail($email)
    {
        $this->getDocument()->fillField('Email', $email);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyBirthday($birthday)
    {
        $this->getDocument()->fillField('Birthday', $birthday);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyPassword($password)
    {
        $this->getDocument()->fillField('Password', $password);
    }

    /**
     * {@inheritdoc}
     */
    public function chooseGender($gender)
    {
        $this->getDocument()->selectFieldOption('Gender', $gender);
    }

    /**
     * {@inheritdoc}
     */
    public function chooseGroup($group)
    {
        $this->getDocument()->selectFieldOption('Group', $group);
    }

    /**
     * {@inheritdoc}
     */
    public function selectCreateAccount()
    {
        $this->getDocument()->find('css', 'label[for=sylius_customer_createUser]')->click();
    }

    /**
     * {@inheritdoc}
     */
    public function hasPasswordField()
    {
        return null !== $this->getDocument()->find('css', '#sylius_customer_user_plainPassword');
    }

    /**
     * {@inheritdoc}
     */
    public function hasCheckedCreateOption()
    {
        return $this->getElement('create_customer_user')->hasAttribute('checked');
    }

    /**
     * {@inheritdoc}
     */
    public function hasCreateOption()
    {
        return null !== $this->getDocument()->find('css', '#sylius_customer_createUser');
    }

    /**
     * {@inheritdoc}
     */
    public function isUserFormHidden()
    {
        return false !== strpos($this->getElement('user_form')->getAttribute('style'), 'none');
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
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
