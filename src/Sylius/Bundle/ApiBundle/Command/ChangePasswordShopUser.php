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

namespace Sylius\Bundle\ApiBundle\Command;

/**
 * @experimental
 * @psalm-immutable
 */
class ChangePasswordShopUser
{
    /** @var string|null */
    public $password;

    /** @var string|null */
    public $confirmPassword;

    /** @var string|null */
    public $oldPassword;

    public function __construct(?string $password, ?string $confirmPassword, ?string $oldPassword)
    {
        $this->password = $password;
        $this->confirmPassword = $confirmPassword;
        $this->oldPassword = $oldPassword;
    }
}
