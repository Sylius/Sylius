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

namespace Sylius\Bundle\CoreBundle\Message\Admin\Account;

class ResetPassword
{
    public function __construct(
        public string $resetPasswordToken,
        public ?string $newPassword = null,
        public ?string $confirmNewPassword = null,
    ) {
    }
}
