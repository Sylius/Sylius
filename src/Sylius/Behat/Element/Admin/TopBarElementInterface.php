<?php

declare(strict_types=1);

namespace Sylius\Behat\Element\Admin;

interface TopBarElementInterface
{
    public function hasAvatarInMainBar(string $avatarPath): bool;
}
