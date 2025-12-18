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

namespace Sylius\Bundle\ApiBundle\Command\Cart;

use Sylius\Bundle\ApiBundle\Attribute\ChannelCodeAware;
use Sylius\Bundle\ApiBundle\Attribute\LocaleCodeAware;
use Sylius\Bundle\ApiBundle\Attribute\LoggedInCustomerEmailAware;

#[ChannelCodeAware]
#[LocaleCodeAware]
#[LoggedInCustomerEmailAware]
class PickupCart
{
    public function __construct(
        public readonly string $channelCode,
        public readonly string $localeCode,
        public readonly ?string $email = null,
        public readonly ?string $tokenValue = null,
    ) {
    }
}
