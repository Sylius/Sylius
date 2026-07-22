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

namespace Sylius\Bundle\CoreBundle\Command\Shop\Account;

/**
 * @experimental
 */
class RequestShopUserVerification
{
    public function __construct(
        public readonly mixed $shopUserId,
        public readonly string $channelCode,
        public readonly string $localeCode,
    ) {
    }
}
