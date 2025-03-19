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

namespace Sylius\Bundle\ApiBundle\Command\Account;

class SendResetPasswordEmail
{
    public function __construct(
        public readonly string $email,
        public readonly string $channelCode,
        public readonly string $localeCode,
    ) {
    }
}
