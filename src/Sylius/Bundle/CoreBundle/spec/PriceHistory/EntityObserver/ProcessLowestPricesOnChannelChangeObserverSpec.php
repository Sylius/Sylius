<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\PriceHistory\EntityObserver;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\PriceHistory\EntityObserver\EntityObserverInterface;
use Sylius\Bundle\CoreBundle\PriceHistory\Processor\ProductLowestPriceBeforeDiscountProcessorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPriceHistoryConfigInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class ProcessLowestPricesOnChannelChangeObserverSpec extends ObjectBehavior
{
    function let(
        ProductLowestPriceBeforeDiscountProcessorInterface $productLowestPriceBeforeDiscountProcessor,
        RepositoryInterface $channelPricingRepository,
    ): void {
        $this->beConstructedWith($productLowestPriceBeforeDiscountProcessor, $channelPricingRepository, 2);
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

    function it_processes_product_lowest_price_for_each_channel_pricing_within_channel(
        ProductLowestPriceBeforeDiscountProcessorInterface $productLowestPriceBeforeDiscountProcessor,
        RepositoryInterface $channelPricingRepository,
        ChannelInterface $channel,
        ChannelPricingInterface $firstChannelPricing,
        ChannelPricingInterface $secondChannelPricing,
        ChannelPricingInterface $thirdChannelPricing,
        ChannelPricingInterface $fourthChannelPricing,
        ChannelPricingInterface $fifthChannelPricing,
    ): void {
        $channel->getCode()->willReturn('WEB');

        $batches = [
            [
                $firstChannelPricing->getWrappedObject(),
                $secondChannelPricing->getWrappedObject(),
            ],
            [
                $thirdChannelPricing->getWrappedObject(),
                $fourthChannelPricing->getWrappedObject(),
            ],
            [
                $fifthChannelPricing->getWrappedObject(),
            ],
            [],
        ];

        $batchSize = 2;

        foreach ($batches as $key => $batch) {
            $channelPricingRepository
                ->findBy(['channelCode' => 'WEB'], ['id' => 'ASC'], 2, $key * $batchSize)
                ->willReturn($batch)
                ->shouldBeCalled()
            ;
        }

        $productLowestPriceBeforeDiscountProcessor->process($firstChannelPricing)->shouldBeCalled();
        $productLowestPriceBeforeDiscountProcessor->process($secondChannelPricing)->shouldBeCalled();
        $productLowestPriceBeforeDiscountProcessor->process($thirdChannelPricing)->shouldBeCalled();
        $productLowestPriceBeforeDiscountProcessor->process($fourthChannelPricing)->shouldBeCalled();
        $productLowestPriceBeforeDiscountProcessor->process($fifthChannelPricing)->shouldBeCalled();

        $this->onChange($channel);
    }

    function it_throws_an_exception_if_entity_is_not_channel(
        ProductLowestPriceBeforeDiscountProcessorInterface $productLowestPriceBeforeDiscountProcessor,
        RepositoryInterface $channelPricingRepository,
        OrderInterface $order,
    ): void {
        $channelPricingRepository->findBy(Argument::any())->shouldNotBeCalled();

        $productLowestPriceBeforeDiscountProcessor->process(Argument::any())->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->during('onChange', [$order]);
    }
}
