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

use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    /**
     * {@inheritdoc}
     */
    public function changeUsername($username)
    {
        $this->getElement('username')->setValue($username);
    }

    /**
     * {@inheritdoc}
     */
    public function changeEmail($email)
    {
        $this->getElement('email')->setValue($email);
    }

    /**
     * {@inheritdoc}
     */
    public function changePassword($password)
    {
        $this->getElement('password')->setValue($password);
    }

    /**
     * {@inheritdoc}
     */
    public function changeLocale($localeCode)
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
            'password' => '#sylius_admin_user_plainPassword',
            'username' => '#sylius_admin_user_username',
        ]);
    }
}
