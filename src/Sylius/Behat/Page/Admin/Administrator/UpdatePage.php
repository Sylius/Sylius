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
    use FormAwareTrait;

    public function removeAvatar(): void
    {
        $this->getElement('button_delete_avatar')->click();
    }

    public function hasAvatar(string $avatarPath): bool
    {
        $srcPath = $this->getAvatarImagePath();

        return str_contains($srcPath, $avatarPath);
    }

    public function changeLocale(string $localeCode): void
    {
        $this->getElement('locale-switch')->selectOption($localeCode);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), $this->getDefinedFormElements(), [
            'button_delete_avatar' => '[data-test-delete-avatar-button]',
            'locale-switch' => '[data-test-admin-locale-switch]',
        ]);
    }

    private function getAvatarImagePath(): string
    {
        $avatarImage = $this->getElement('avatar_image');
        $imagePath = $avatarImage->getAttribute('data-test-avatar-image');

        if (null === $imagePath) {
            return '';
        }

        return $imagePath;
    }
}
