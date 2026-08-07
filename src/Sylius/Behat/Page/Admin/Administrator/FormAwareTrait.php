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

use Sylius\Behat\Context\Ui\Admin\Helper\SecurePasswordTrait;

trait FormAwareTrait
{
    use SecurePasswordTrait;

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
        $this->getElement('field_password')->setValue($this->replaceWithSecurePassword($password));
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

    public function grantAdministrationAccess(): void
    {
        $this->getElement('field_administration_access')->check();
    }

    public function revokeAdministrationAccess(): void
    {
        $this->getElement('field_administration_access')->uncheck();
    }

    public function hasAdministrationAccess(): bool
    {
        return (bool) $this->getElement('field_administration_access')->getValue();
    }

    public function grantApiAccess(): void
    {
        $this->getElement('field_api_access')->check();
    }

    public function revokeApiAccess(): void
    {
        $this->getElement('field_api_access')->uncheck();
    }

    public function hasApiAccess(): bool
    {
        return (bool) $this->getElement('field_api_access')->getValue();
    }

    public function getAccessLevelsValidationMessage(): string
    {
        return $this->getElement('access_levels_validation_message')->getText();
    }

    public function isAvatarAttached(): bool
    {
        return $this->getElement('avatar_image')->getAttribute('data-test-avatar-image') !== '';
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
            'access_levels_validation_message' => '[data-test-access-levels-validation-message]',
            'avatar_image' => '[data-test-avatar-image]',
            'field_administration_access' => '#sylius_admin_admin_user_administrationAccess',
            'field_api_access' => '#sylius_admin_admin_user_apiAccess',
            'field_avatar' => '#sylius_admin_admin_user_avatar_file',
            'field_email' => '#sylius_admin_admin_user_email',
            'field_enabled' => '#sylius_admin_admin_user_enabled',
            'field_first_name' => '#sylius_admin_admin_user_firstName',
            'field_last_name' => '#sylius_admin_admin_user_lastName',
            'field_locale_code' => '#sylius_admin_admin_user_localeCode',
            'field_name' => '#sylius_admin_admin_user_username',
            'field_password' => '#sylius_admin_admin_user_plainPassword',
            'field_username' => '#sylius_admin_admin_user_username',
        ];
    }
}
