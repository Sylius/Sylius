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

namespace Tests\Sylius\Component\Core\Factory;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Factory\ChannelPricingLogEntryFactory;
use Sylius\Component\Core\Factory\ChannelPricingLogEntryFactoryInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ChannelPricingLogEntry;
use Sylius\Resource\Exception\UnsupportedMethodException;
use Sylius\Resource\Model\ResourceInterface;

final class ChannelPricingLogEntryFactoryTest extends TestCase
{
    private ChannelPricingLogEntryFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new ChannelPricingLogEntryFactory(ChannelPricingLogEntry::class);
    }

    public function testShouldImplementChannelPricingLogEntryFactoryInterface(): void
    {
        $this->assertInstanceOf(ChannelPricingLogEntryFactoryInterface::class, $this->factory);
    }

    public function testShouldThrowExceptionWhenInvalidClassNameIsPassed(): void
    {
        $this->expectException(\DomainException::class);

        new ChannelPricingLogEntryFactory(ResourceInterface::class);
    }

    public function testShouldThrowExceptionWhenCreateNewIsCalled(): void
    {
        $this->expectException(UnsupportedMethodException::class);

        $this->factory->createNew();
    }

    public function testShouldCreateChannelPricingLogEntry(): void
    {
        $channelPricing = $this->createMock(ChannelPricingInterface::class);
        $date = new \DateTimeImmutable();
        $price = 1000;
        $originalPrice = 2000;

        $this->assertEquals(
            new ChannelPricingLogEntry(
                $channelPricing,
                $date,
                $price,
                $originalPrice,
            ),
            $this->factory->create($channelPricing, $date, $price, $originalPrice),
        );
    }
}
