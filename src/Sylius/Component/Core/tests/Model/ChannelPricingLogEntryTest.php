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

namespace Tests\Sylius\Component\Core\Model;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ChannelPricingLogEntry;
use Sylius\Component\Core\Model\ChannelPricingLogEntryInterface;

final class ChannelPricingLogEntryTest extends TestCase
{
    private ChannelPricingInterface&MockObject $channelPricing;

    private ChannelPricingLogEntry $channelPricingLogEntry;

    protected function setUp(): void
    {
        $this->channelPricing = $this->createMock(ChannelPricingInterface::class);
        $this->channelPricingLogEntry = new ChannelPricingLogEntry(
            $this->channelPricing,
            new \DateTime(),
            1000,
            2000,
        );
    }

    public function testShouldImplementChannelPricingLogEntryInterface(): void
    {
        $this->assertInstanceOf(ChannelPricingLogEntryInterface::class, $this->channelPricingLogEntry);
    }

    public function testShouldInitializeWithNoOriginalPrice(): void
    {
        $this->channelPricingLogEntry = new ChannelPricingLogEntry(
            $this->channelPricing,
            new \DateTime(),
            1000,
            null,
        );

        $this->assertNull($this->channelPricingLogEntry->getOriginalPrice());
    }

    public function testShouldGetsChannelPricing(): void
    {
        $this->assertInstanceOf(ChannelPricingInterface::class, $this->channelPricingLogEntry->getChannelPricing());
    }

    public function testShouldGetsPrice(): void
    {
        $this->assertSame(1000, $this->channelPricingLogEntry->getPrice());
    }

    public function testShouldGetsOriginalPrice(): void
    {
        $this->assertSame(2000, $this->channelPricingLogEntry->getOriginalPrice());
    }

    public function testShouldGetsLoggedAt(): void
    {
        $this->assertInstanceOf(\DateTimeInterface::class, $this->channelPricingLogEntry->getLoggedAt());
    }
}
