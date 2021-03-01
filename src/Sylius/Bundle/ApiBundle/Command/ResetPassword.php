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
class ResetPassword implements ResetPasswordTokenAwareInterface
{
    /** @var string */
    public $newPassword;

    /** @var string */
    public $confirmNewPassword;

    /** @var string */
    public $resetPasswordToken;

    public function __construct(string $newPassword, string $confirmNewPassword)
    {
        $this->newPassword = $newPassword;
        $this->confirmNewPassword = $confirmNewPassword;
    }

    public function getResetPasswordToken(): string
    {
        return $this->resetPasswordToken;
    }

    public function setResetPasswordToken(string $token): void
    {
        $this->resetPasswordToken = $token;
    }
}
