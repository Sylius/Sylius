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

namespace Tests\Sylius\Bundle\CoreBundle\Provider;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Provider\ChannelBasedDefaultTaxZoneProvider;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Provider\ZoneProviderInterface;

final class ChannelBasedDefaultTaxZoneProviderTest extends TestCase
{
    private ChannelBasedDefaultTaxZoneProvider $channelBasedDefaultTaxZoneProvider;

    protected function setUp(): void
    {
        $this->channelBasedDefaultTaxZoneProvider = new ChannelBasedDefaultTaxZoneProvider();
    }

    public function testImplementsDefaultTaxZoneProviderInterface(): void
    {
        $this->assertInstanceOf(ZoneProviderInterface::class, $this->channelBasedDefaultTaxZoneProvider);
    }

    public function testProvidesDefaultTaxZoneFromOrderChannel(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $order = $this->createMock(OrderInterface::class);
        $defaultTaxZone = $this->createMock(ZoneInterface::class);

        $order->expects($this->once())->method('getChannel')->willReturn($channel);
        $channel->expects($this->once())->method('getDefaultTaxZone')->willReturn($defaultTaxZone);

        $this->assertSame($defaultTaxZone, $this->channelBasedDefaultTaxZoneProvider->getZone($order));
    }
}
