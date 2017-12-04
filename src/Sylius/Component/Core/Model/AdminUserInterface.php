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

interface AdminUserInterface extends BaseUserInterface
{
    public const DEFAULT_ADMIN_ROLE = 'ROLE_ADMINISTRATION_ACCESS';

    /**
     * @return string|null
     */
    public function getFirstName(): ?string;

    /**
     * @param string|null $firstName
     */
    public function setFirstName(?string $firstName): void;

    /**
     * @return string|null
     */
    public function getLastName(): ?string;

    /**
     * @param string|null $lastName
     */
    public function setLastName(?string $lastName): void;

    /**
     * @return string|null
     */
    public function getLocaleCode(): ?string;

    /**
     * @param string|null $code
     */
    public function setLocaleCode(?string $code): void;
}
