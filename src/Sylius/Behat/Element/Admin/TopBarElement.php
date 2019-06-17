<?php

declare(strict_types=1);

namespace Sylius\Behat\Element\Admin;

use FriendsOfBehat\PageObjectExtension\Element\Element;

final class TopBarElement extends Element implements TopBarElementInterface
{
    public function hasAvatarInMainBar(string $avatarPath): bool
    {
        $image = $this->getDocument()->find('css', 'img.ui.avatar.image');

        if (null === $image) {
            return false;
        }

        return strpos($image->getAttribute('src'), $avatarPath) !== false;
    }
}
