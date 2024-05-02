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
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPriceHistoryConfigInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class ProcessLowestPricesOnChannelPriceHistoryConfigChangeObserverSpec extends ObjectBehavior
{
    function let(
        ChannelRepositoryInterface $channelRepository,
        ApplyLowestPriceOnChannelPricingsCommandDispatcherInterface $commandDispatcher,
    ): void {
        $this->beConstructedWith($channelRepository, $commandDispatcher);
    }

    function it_is_an_entity_observer(): void
    {
        $this->shouldImplement(EntityObserverInterface::class);
    }

    function it_does_not_support_anything_other_than_channel_price_history_config_interface(
        OrderInterface $order,
    ): void {
        $this->supports($order)->shouldReturn(false);
    }

    function it_does_not_support_new_configs(ChannelPriceHistoryConfigInterface $config): void
    {
        $config->getId()->willReturn(null);

        $this->supports($config)->shouldReturn(false);
    }

    function it_only_supports_existing_configs(ChannelPriceHistoryConfigInterface $config): void
    {
        $config->getId()->willReturn(1);

        $this->supports($config)->shouldReturn(true);
    }

    function it_does_not_support_a_config_that_is_currently_being_processed(
        ChannelPriceHistoryConfigInterface $config,
    ): void {
        $config->getId()->willReturn(1);

        $object = $this->object->getWrappedObject();
        $objectReflection = new \ReflectionObject($object);
        $property = $objectReflection->getProperty('configsCurrentlyProcessed');
        $property->setAccessible(true);
        $property->setValue($object, [1 => true]);

        $this->supports($config)->shouldReturn(false);
    }

    function it_observes_lowest_price_for_discounted_products_checking_period_field(): void
    {
        $this->observedFields()->shouldReturn(['lowestPriceForDiscountedProductsCheckingPeriod']);
    }

    function it_throws_an_exception_when_entity_is_not_a_channel_price_history_config_interface(
        ChannelRepositoryInterface $channelRepository,
        ApplyLowestPriceOnChannelPricingsCommandDispatcherInterface $commandDispatcher,
        OrderInterface $order,
    ): void {
        $channelRepository->findOneBy(Argument::any())->shouldNotBeCalled();
        $commandDispatcher->applyWithinChannel(Argument::any())->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->during('onChange', [$order]);
    }

    function it_does_nothing_when_config_has_no_channel_counterpart(
        ChannelRepositoryInterface $channelRepository,
        ApplyLowestPriceOnChannelPricingsCommandDispatcherInterface $commandDispatcher,
        ChannelPriceHistoryConfigInterface $config,
    ): void {
        $channelRepository->findOneBy(['channelPriceHistoryConfig' => $config])->willReturn(null);

        $commandDispatcher->applyWithinChannel(Argument::any())->shouldNotBeCalled();

        $this->onChange($config);
    }

    function it_delegates_processing_lowest_prices_to_command_dispatcher(
        ChannelRepositoryInterface $channelRepository,
        ApplyLowestPriceOnChannelPricingsCommandDispatcherInterface $commandDispatcher,
        ChannelInterface $channel,
        ChannelPriceHistoryConfigInterface $config,
    ): void {
        $channelRepository->findOneBy(['channelPriceHistoryConfig' => $config])->willReturn($channel);

        $commandDispatcher->applyWithinChannel($channel)->shouldBeCalled();

        $this->onChange($config);
    }
}
