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

use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    public function attachAvatar(string $avatar): void
    {
        $filesPath = $this->getParameter('files_path');

        $imageForm = $this->getElement('add_avatar')->find('css', 'input[type="file"]');

        $imageForm->attachFile($filesPath . $avatar);
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

    public function hasAvatar(string $avatarPath, string $avatar): bool
    {
        return $this->isAvatar($avatarPath, $avatar, 'add_avatar');
    }

    public function hasAvatarInMainBar(string $avatarPath, string $avatar): bool
    {
        return $this->isAvatar($avatarPath, $avatar, 'main_bar_avatar');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'email' => '#sylius_admin_user_email',
            'enabled' => '#sylius_admin_user_enabled',
            'locale_code' => '#sylius_admin_user_localeCode',
            'password' => '#sylius_admin_user_plainPassword',
            'username' => '#sylius_admin_user_username',
            'add_avatar' => '#add-avatar',
            'main_bar_avatar' => '.ui.avatar.image'
        ]);
    }

    private function isAvatar(string $avatarPath, string $avatar, string $element): bool
    {
        $srcPath = $this->getElement($element)->find('css', 'img')->getAttribute('src');

        return strpos($srcPath, $avatarPath) !== false;
    }
}
