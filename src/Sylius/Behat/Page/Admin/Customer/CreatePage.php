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
    public function selectCreateAccount()
    {
        $this->getDocument()->find('css', 'label[for=sylius_customer_create_user]')->click();
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'first name' => '#sylius_customer_firstName',
            'last name' => '#sylius_customer_lastName',
            'email' => '#sylius_customer_email',
        ]);
    }
}
