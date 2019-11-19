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

namespace Sylius\Component\Channel\Model;

interface ChannelTypeInterface
{
    public const TYPE_WEBSITE = 'website';

    public const TYPE_MOBILE = 'mobile';

    public const TYPE_POS = 'pos';
}
