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

namespace spec\Sylius\Bundle\CoreBundle\PriceHistory\EntityObserver;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\PriceHistory\EntityObserver\EntityObserverInterface;
use Sylius\Bundle\CoreBundle\PriceHistory\Logger\PriceChangeLoggerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class CreateLogEntryOnPriceChangeObserverSpec extends ObjectBehavior
{
    function let(PriceChangeLoggerInterface $priceChangeLogger): void
    {
        $this->beConstructedWith($priceChangeLogger);
    }

    function it_implements_on_entity_change_interface(): void
    {
        $this->shouldImplement(EntityObserverInterface::class);
    }

    function it_supports_channel_pricing_with_price_specified_only(
        ChannelPricingInterface $channelPricingWithPrice,
        ChannelPricingInterface $channelPricingWithoutPrice,
        OrderInterface $order,
    ): void {
        $channelPricingWithPrice->getPrice()->willReturn(1000);

        $this->supports($channelPricingWithPrice)->shouldReturn(true);
        $this->supports($channelPricingWithoutPrice)->shouldReturn(false);
        $this->supports($order)->shouldReturn(false);
    }

    function it_supports_price_and_original_price_fields(): void
    {
        $this->observedFields()->shouldReturn(['price', 'originalPrice']);
    }

    function it_logs_price_change(
        PriceChangeLoggerInterface $priceChangeLogger,
        ChannelPricingInterface $channelPricing,
    ): void {
        $priceChangeLogger->log($channelPricing)->shouldBeCalled();

        $this->onChange($channelPricing);
    }

    function it_throws_an_error_if_entity_is_not_channel_pricing(
        PriceChangeLoggerInterface $priceChangeLogger,
        ChannelInterface $channel,
    ): void {
        $priceChangeLogger->log($channel)->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->during('onChange', [$channel]);
    }
}
