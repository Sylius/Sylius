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

use Sylius\Bundle\ApiBundle\Attribute\ChannelCodeAware;
use Sylius\Bundle\ApiBundle\Attribute\LocaleCodeAware;

#[ChannelCodeAware]
#[LocaleCodeAware]
readonly class RegisterShopUser
{
    public function __construct(
        public string $channelCode,
        public string $localeCode,
        public string $firstName,
        public string $lastName,
        public string $email,
        public string $password,
        public bool $subscribedToNewsletter = false,
    ) {
    }
}
