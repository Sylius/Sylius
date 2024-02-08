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

use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    public function attachAvatar(string $path): void
    {
        $filesPath = $this->getParameter('files_path');

        $imageForm = $this->getElement('add_avatar')->find('css', 'input[type="file"]');

        $imageForm->attachFile($filesPath . $path);
    }

    public function changeUsername(string $username): void
    {
        $this->getElement('username')->setValue($username);
    }

    public function changeEmail(string $email): void
    {
        $this->getElement('email')->setValue($email);
    }

    public function changePassword(string $password): void
    {
        $this->getElement('password')->setValue($password);
    }

    public function changeLocale(string $localeCode): void
    {
        $this->getElement('locale_code')->selectOption($localeCode);
    }

    public function removeAvatar(): void
    {
        $this->getElement('remove_avatar')->click();
    }

    public function hasAvatar(string $avatarPath): bool
    {
        $srcPath = $this->getAvatarImagePath();

        return str_contains($srcPath, $avatarPath);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'add_avatar' => '#add-avatar',
            'email' => '#sylius_admin_user_email',
            'enabled' => '#sylius_admin_user_enabled',
            'locale_code' => '#sylius_admin_user_localeCode',
            'password' => '#sylius_admin_user_plainPassword',
            'remove_avatar' => '.ui.icon.red.labeled.button',
            'username' => '#sylius_admin_user_username',
        ]);
    }

    private function getAvatarImagePath(): string
    {
        $image = $this->getElement('add_avatar')->find('css', 'img');

        if (null === $image) {
            return '';
        }

        return $image->getAttribute('src');
    }
}
