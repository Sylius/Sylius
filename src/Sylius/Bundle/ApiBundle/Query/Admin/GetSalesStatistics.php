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
use Sylius\Component\Core\Sales\ValueObject\SalesPeriod;

/** @experimental */
class GetSalesStatistics implements ChannelCodeAwareInterface
{
    public function __construct(private SalesPeriod $salesPeriod, private ?string $channelCode = null)
    {
    }

    public function getSalesPeriod(): SalesPeriod
    {
        return $this->salesPeriod;
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
