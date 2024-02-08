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

namespace Sylius\Component\Core\Model;

use Sylius\Component\User\Model\UserInterface as BaseUserInterface;

interface AdminUserInterface extends BaseUserInterface, ImageAwareInterface
{
    public const DEFAULT_ADMIN_ROLE = 'ROLE_ADMINISTRATION_ACCESS';

    public function getFirstName(): ?string;

    public function setFirstName(?string $firstName): void;

    public function getLastName(): ?string;

    public function setLastName(?string $lastName): void;

    public function getLocaleCode(): ?string;

    public function setLocaleCode(?string $code): void;

    public function getAvatar(): ?ImageInterface;

    public function setAvatar(?ImageInterface $avatar);
}
