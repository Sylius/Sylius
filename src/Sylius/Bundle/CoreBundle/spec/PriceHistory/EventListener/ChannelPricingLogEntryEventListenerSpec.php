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

namespace spec\Sylius\Bundle\CoreBundle\PriceHistory\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\PriceHistory\Processor\ProductLowestPriceBeforeDiscountProcessorInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ChannelPricingLogEntryInterface;

final class ChannelPricingLogEntryEventListenerSpec extends ObjectBehavior
{
    function let(ProductLowestPriceBeforeDiscountProcessorInterface $lowestPriceProcessor): void
    {
        $this->beConstructedWith($lowestPriceProcessor);
    }

    function it_does_nothing_when_object_is_not_channel_pricing_log_entry(
        LifecycleEventArgs $event,
        ChannelPricingInterface $channelPricing,
    ): void {
        $event->getObject()->willReturn($channelPricing);

        $this->postPersist($event);
    }

    function it_processes_lowest_price_for_channel_pricing(
        ProductLowestPriceBeforeDiscountProcessorInterface $lowestPriceProcessor,
        ChannelPricingInterface $channelPricing,
        ChannelPricingLogEntryInterface $channelPricingLogEntry,
        LifecycleEventArgs $event,
    ): void {
        $event->getObject()->willReturn($channelPricingLogEntry);
        $channelPricingLogEntry->getChannelPricing()->willReturn($channelPricing);
        $lowestPriceProcessor->process($channelPricing)->shouldBeCalled();

        $this->postPersist($event);
    }
}
