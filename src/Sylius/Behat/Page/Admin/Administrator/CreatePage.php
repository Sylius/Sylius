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

namespace Sylius\Behat\Page\Admin\Administrator;

use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    public function enable(): void
    {
        $this->getElement('enabled')->check();
    }

    public function specifyUsername(string $username): void
    {
        $this->getElement('name')->setValue($username);
    }

    public function specifyEmail(string $email): void
    {
        $this->getElement('email')->setValue($email);
    }

    public function specifyPassword(string $password): void
    {
        $this->getElement('password')->setValue($password);
    }

    public function specifyLocale(string $localeCode): void
    {
        $this->getElement('locale_code')->selectOption($localeCode);
    }

    public function specifyTimezone(string $timezone): void
    {
        $this->getElement('timezone')->selectOption($timezone);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'email' => '#sylius_admin_user_email',
            'enabled' => '#sylius_admin_user_enabled',
            'locale_code' => '#sylius_admin_user_localeCode',
            'name' => '#sylius_admin_user_username',
            'password' => '#sylius_admin_user_plainPassword',
            'timezone' => '#sylius_admin_user_timezone',
        ]);
    }
}
