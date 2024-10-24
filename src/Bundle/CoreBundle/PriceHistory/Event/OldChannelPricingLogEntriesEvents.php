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

namespace Sylius\Bundle\CoreBundle\PriceHistory\Event;

interface OldChannelPricingLogEntriesEvents
{
    public const PRE_REMOVE = 'sylius.old_channel_pricing_log_entries.pre_remove';

    public const POST_REMOVE = 'sylius.old_channel_pricing_log_entries.post_remove';
}
