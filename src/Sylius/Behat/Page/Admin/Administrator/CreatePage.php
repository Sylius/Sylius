<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Administrator;

use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    public function enable()
    {
        $this->getElement('enabled')->check();
    }

    /**
     * {@inheritdoc}
     */
    public function specifyUsername($username)
    {
        $this->getElement('name')->setValue($username);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyEmail($email)
    {
        $this->getElement('email')->setValue($email);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyPassword($password)
    {
        $this->getElement('password')->setValue($password);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyLocale($localeCode)
    {
        $this->getElement('locale_code')->selectOption($localeCode);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'email' => '#sylius_admin_user_email',
            'enabled' => '#sylius_admin_user_enabled',
            'locale_code' => '#sylius_admin_user_localeCode',
            'name' => '#sylius_admin_user_username',
            'password' => '#sylius_admin_user_plainPassword',
        ]);
    }
}
