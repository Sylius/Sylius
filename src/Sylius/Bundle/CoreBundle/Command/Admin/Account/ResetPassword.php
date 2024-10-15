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

namespace Sylius\Bundle\CoreBundle\Command\Admin\Account;

class ResetPassword
{
    public function __construct(
        public readonly string $token,
        #[\SensitiveParameter]
        public readonly string $newPassword,
        #[\SensitiveParameter]
        public readonly ?string $confirmNewPassword = null,
    ) {
    }
}
