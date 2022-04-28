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

/** @experimental */
class ResetPassword
{
    /** @var string|null */
    public $newPassword;

    /** @var string|null */
    public $confirmNewPassword;

    /** @var string */
    public $resetPasswordToken;

    public function __construct(string $resetPasswordToken)
    {
        $this->resetPasswordToken = $resetPasswordToken;
    }
}
