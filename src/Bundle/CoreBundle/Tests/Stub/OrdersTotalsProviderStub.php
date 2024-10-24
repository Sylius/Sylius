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

namespace Sylius\Bundle\CoreBundle\Tests\Stub;

use Sylius\Bundle\CoreBundle\Attribute\AsOrdersTotalsProvider;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Statistics\Provider\OrdersTotals\OrdersTotalsProviderInterface;

#[AsOrdersTotalsProvider(type: 'stub')]
final class OrdersTotalsProviderStub implements OrdersTotalsProviderInterface
{
    public function provideForPeriodInChannel(\DatePeriod $period, ChannelInterface $channel): array
    {
        return [];
    }
}
