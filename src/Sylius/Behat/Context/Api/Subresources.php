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

namespace Sylius\Behat\Context\Api;

final class Subresources
{
    public const PROMOTION_COUPONS = 'coupons';

    public const ADDRESSES_LOG_ENTRIES = 'log-entries';

    private function __construct()
    {
    }
}
