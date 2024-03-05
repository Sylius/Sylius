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

namespace Sylius\Behat\Page\Admin\Administrator;

trait FormAwareTrait
{
    public function setFirstName(string $firstName): void
    {
        $this->getElement('field_first_name')->setValue($firstName);
    }

    public function getFirstName(): string
    {
        return $this->getElement('field_first_name')->getValue();
    }

    public function setLastName(string $lastName): void
    {
        $this->getElement('field_last_name')->setValue($lastName);
    }

    public function getLastName(): string
    {
        return $this->getElement('field_last_name')->getValue();
    }

    public function setUsername(string $username): void
    {
        $this->getElement('field_username')->setValue($username);
    }

    public function getUsername(): string
    {
        return $this->getElement('field_username')->getValue();
    }

    public function setEmail(string $email): void
    {
        $this->getElement('field_email')->setValue($email);
    }

    public function getEmail(): string
    {
        return $this->getElement('field_email')->getValue();
    }

    public function setPassword(string $password): void
    {
        $this->getElement('field_password')->setValue($password);
    }

    public function getPassword(): string
    {
        return $this->getElement('field_password')->getValue();
    }

    public function setLocale(string $locale): void
    {
        $this->getElement('field_locale_code')->setValue($locale);
    }

    public function getLocale(): string
    {
        return $this->getElement('field_locale_code')->getValue();
    }

    public function enable(): void
    {
        $this->getElement('field_enabled')->check();
    }

    public function disable(): void
    {
        $this->getElement('field_enabled')->uncheck();
    }

    public function isEnabled(): bool
    {
        return $this->getElement('field_enabled')->getValue();
    }

    public function isAvatarAttached(): bool
    {
        return $this->getElement('field_avatar')->getValue() !== '';
    }

    public function attachAvatar(string $path): void
    {
        $filesPath = $this->getParameter('files_path');
        $avatarField = $this->getElement('field_avatar');
        $avatarField->attachFile($filesPath . $path);
    }

    /**
     * @return array<string, string>
     */
    protected function getDefinedFormElements(): array
    {
        return [
            'avatar_image' => '[data-test-avatar-image]',
            'field_avatar' => '#sylius_admin_user_avatar_file',
            'field_email' => '#sylius_admin_user_email',
            'field_enabled' => '#sylius_admin_user_enabled',
            'field_locale_code' => '#sylius_admin_user_localeCode',
            'field_name' => '#sylius_admin_user_username',
            'field_password' => '#sylius_admin_user_plainPassword',
            'field_username' => '#sylius_admin_user_username',
        ];
    }
}
