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
        $image = $this->getDocument()->find('css', 'img.ui.avatar.image');

        if (null === $image) {
            return '';
        }

        return $image->getAttribute('src');
    }
}
