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

namespace Sylius\Component\Core\Model;

use Sylius\Component\User\Model\UserInterface as BaseUserInterface;
use Symfony\Component\Security\Core\Encoder\EncoderAwareInterface;

interface AdminUserInterface extends BaseUserInterface, EncoderAwareInterface
{
    public const DEFAULT_ADMIN_ROLE = 'ROLE_ADMINISTRATION_ACCESS';

    public function getFirstName(): ?string;

    public function setFirstName(?string $firstName): void;

    public function getLastName(): ?string;

    public function setLastName(?string $lastName): void;

    public function getLocaleCode(): ?string;

    public function setLocaleCode(?string $code): void;
}
