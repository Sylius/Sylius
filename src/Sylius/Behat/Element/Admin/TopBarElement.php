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

namespace Sylius\Behat\Element\Admin;

use FriendsOfBehat\PageObjectExtension\Element\Element;

final class TopBarElement extends Element implements TopBarElementInterface
{
    public function hasAvatarInMainBar(string $avatarPath): bool
    {
        return strpos($this->getAvatarImagePath(), $avatarPath) !== false;
    }

    public function hasDefaultAvatarInMainBar(): bool
    {
        return strpos($this->getAvatarImagePath(), '/assets/admin/img/50x50.png') !== false;
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
