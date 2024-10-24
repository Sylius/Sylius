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
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\PriceHistory\CommandDispatcher\ApplyLowestPriceOnChannelPricingsCommandDispatcherInterface;
use Sylius\Bundle\CoreBundle\PriceHistory\EntityObserver\EntityObserverInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPriceHistoryConfigInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class ProcessLowestPricesOnChannelChangeObserverSpec extends ObjectBehavior
{
    function let(ApplyLowestPriceOnChannelPricingsCommandDispatcherInterface $commandDispatcher): void
    {
        $this->beConstructedWith($commandDispatcher);
    }

    function it_is_an_entity_observer(): void
    {
        $this->shouldImplement(EntityObserverInterface::class);
    }

    function it_does_not_support_anything_other_than_channel_interface(OrderInterface $order): void
    {
        $this->supports($order)->shouldReturn(false);
    }

    function it_does_not_support_a_channel_that_is_currently_being_processed(
        ChannelInterface $channel,
    ): void {
        $channel->getCode()->willReturn('test');
        $channel->getChannelPriceHistoryConfig()->shouldNotBeCalled();

        $object = $this->object->getWrappedObject();
        $objectReflection = new \ReflectionObject($object);
        $property = $objectReflection->getProperty('channelsCurrentlyProcessed');
        $property->setAccessible(true);
        $property->setValue($object, ['test' => true]);

        $this->supports($channel)->shouldReturn(false);
    }

    function it_does_not_support_channels_with_no_price_history_config(ChannelInterface $channel): void
    {
        $channel->getCode()->willReturn('test');
        $channel->getChannelPriceHistoryConfig()->willReturn(null);

        $this->supports($channel)->shouldReturn(false);
    }

    function it_does_not_support_channels_with_existing_price_history_config(
        ChannelInterface $channel,
        ChannelPriceHistoryConfigInterface $config,
    ): void {
        $channel->getCode()->willReturn('test');
        $channel->getChannelPriceHistoryConfig()->willReturn($config);
        $config->getId()->willReturn(12);

        $this->supports($channel)->shouldReturn(false);
    }

    function it_only_supports_channels_with_new_price_history_config(
        ChannelInterface $channel,
        ChannelPriceHistoryConfigInterface $config,
    ): void {
        $channel->getCode()->willReturn('test');
        $channel->getChannelPriceHistoryConfig()->willReturn($config);
        $config->getId()->willReturn(null);

        $this->supports($channel)->shouldReturn(true);
    }

    function it_observes_channel_price_history_config_field(): void
    {
        $this->observedFields()->shouldReturn(['channelPriceHistoryConfig']);
    }

    function it_delegates_processing_lowest_prices_to_command_dispatcher(
        ApplyLowestPriceOnChannelPricingsCommandDispatcherInterface $commandDispatcher,
        ChannelInterface $channel,
    ): void {
        $commandDispatcher->applyWithinChannel($channel)->shouldBeCalled();

        $this->onChange($channel);
    }

    function it_throws_an_exception_if_entity_is_not_channel(
        ApplyLowestPriceOnChannelPricingsCommandDispatcherInterface $commandDispatcher,
        OrderInterface $order,
    ): void {
        $commandDispatcher->applyWithinChannel(Argument::any())->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->during('onChange', [$order]);
    }
}
