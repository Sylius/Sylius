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

namespace Sylius\Behat\Element\Admin;

use FriendsOfBehat\PageObjectExtension\Element\Element;

final class TopBarElement extends Element implements TopBarElementInterface
{
    public function hasAvatarInMainBar(string $avatarPath): bool
    {
        return str_contains($this->getAvatarImagePath(), $avatarPath);
    }

    public function hasDefaultAvatarInMainBar(): bool
    {
        $avatarElement = $this->getDocument()->find('css', 'i.ui.avatar.user.icon');

        return $avatarElement !== null;
    }

    private function getAvatarImagePath(): string
    {
        $userAvatar = $this->getElement('user_avatar');

        return $userAvatar->getAttribute('data-test-user-avatar');
    }

    /**
     * @return array<string, string>
     */
    protected function getDefinedElements(): array
    {
        return [
            'user_avatar' => '[data-test-user-avatar]',
        ];
    }
}
