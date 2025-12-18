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

namespace Tests\Sylius\Bundle\CoreBundle\PriceHistory\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\PriceHistory\EventListener\ChannelPricingLogEntryEventListener;
use Sylius\Bundle\CoreBundle\PriceHistory\Processor\ProductLowestPriceBeforeDiscountProcessorInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ChannelPricingLogEntryInterface;

final class ChannelPricingLogEntryEventListenerTest extends TestCase
{
    private MockObject&ProductLowestPriceBeforeDiscountProcessorInterface $lowestPriceProcessor;

    private ChannelPricingLogEntryEventListener $channelPricingLogEntryEventListener;

    protected function setUp(): void
    {
        $this->lowestPriceProcessor = $this->createMock(ProductLowestPriceBeforeDiscountProcessorInterface::class);
        $this->channelPricingLogEntryEventListener = new ChannelPricingLogEntryEventListener($this->lowestPriceProcessor);
    }

    public function testDoesNothingWhenObjectIsNotChannelPricingLogEntry(): void
    {
        $event = $this->createMock(LifecycleEventArgs::class);
        $channelPricing = $this->createMock(ChannelPricingInterface::class);

        $event->expects($this->once())->method('getObject')->willReturn($channelPricing);

        $this->channelPricingLogEntryEventListener->postPersist($event);
    }

    public function testProcessesLowestPriceForChannelPricing(): void
    {
        $channelPricing = $this->createMock(ChannelPricingInterface::class);
        $channelPricingLogEntry = $this->createMock(ChannelPricingLogEntryInterface::class);
        $event = $this->createMock(LifecycleEventArgs::class);

        $event->expects($this->once())->method('getObject')->willReturn($channelPricingLogEntry);
        $channelPricingLogEntry->expects($this->once())->method('getChannelPricing')->willReturn($channelPricing);
        $this->lowestPriceProcessor->expects($this->once())->method('process')->with($channelPricing);

        $this->channelPricingLogEntryEventListener->postPersist($event);
    }
}
