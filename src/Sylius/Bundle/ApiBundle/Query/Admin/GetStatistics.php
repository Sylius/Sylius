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

namespace Sylius\Bundle\ApiBundle\Query\Admin;

use Sylius\Bundle\ApiBundle\Command\ChannelCodeAwareInterface;
use Sylius\Component\Core\DateTime\Period;

/** @experimental */
class GetStatistics implements ChannelCodeAwareInterface
{
    public function __construct(private Period $period, private ?string $channelCode = null)
    {
    }

    public function getPeriod(): Period
    {
        return $this->period;
    }

    public function getChannelCode(): ?string
    {
        return $this->channelCode;
    }

    public function setChannelCode(?string $channelCode): void
    {
        $this->channelCode = $channelCode;
    }
}
