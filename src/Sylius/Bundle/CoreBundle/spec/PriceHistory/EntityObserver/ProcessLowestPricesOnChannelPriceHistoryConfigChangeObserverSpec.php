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
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPriceHistoryConfigInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class ProcessLowestPricesOnChannelPriceHistoryConfigChangeObserverSpec extends ObjectBehavior
{
    function let(
        ProductLowestPriceBeforeDiscountProcessorInterface $productLowestPriceBeforeDiscountProcessor,
        RepositoryInterface $channelPricingRepository,
        ChannelRepositoryInterface $channelRepository,
    ): void {
        $this->beConstructedWith(
            $productLowestPriceBeforeDiscountProcessor,
            $channelPricingRepository,
            $channelRepository,
            2,
        );
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

    function it_throws_an_exception_if_entity_is_not_channel_price_history_config_interface(
        ProductLowestPriceBeforeDiscountProcessorInterface $productLowestPriceBeforeDiscountProcessor,
        RepositoryInterface $channelPricingRepository,
        OrderInterface $order,
    ): void {
        $channelPricingRepository->findBy(Argument::any())->shouldNotBeCalled();

        $productLowestPriceBeforeDiscountProcessor->process(Argument::any())->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->during('onChange', [$order]);
    }

    function it_does_nothing_when_config_has_no_channel_counterpart(
        ProductLowestPriceBeforeDiscountProcessorInterface $productLowestPriceBeforeDiscountProcessor,
        RepositoryInterface $channelPricingRepository,
        ChannelRepositoryInterface $channelRepository,
        ChannelPriceHistoryConfigInterface $config,
    ): void {
        $channelRepository->findOneBy(['channelPriceHistoryConfig' => $config])->willReturn(null);
        $channelPricingRepository->findBy(Argument::any())->shouldNotBeCalled();

        $productLowestPriceBeforeDiscountProcessor->process(Argument::any())->shouldNotBeCalled();

        $this->onChange($config);
    }

    function it_processes_product_lowest_price_for_each_channel_pricing_within_channel(
        ProductLowestPriceBeforeDiscountProcessorInterface $productLowestPriceBeforeDiscountProcessor,
        RepositoryInterface $channelPricingRepository,
        ChannelRepositoryInterface $channelRepository,
        ChannelPriceHistoryConfigInterface $config,
        ChannelInterface $channel,
        ChannelPricingInterface $firstChannelPricing,
        ChannelPricingInterface $secondChannelPricing,
        ChannelPricingInterface $thirdChannelPricing,
        ChannelPricingInterface $fourthChannelPricing,
        ChannelPricingInterface $fifthChannelPricing,
    ): void {
        $channel->getCode()->willReturn('WEB');
        $channelRepository->findOneBy(['channelPriceHistoryConfig' => $config])->willReturn($channel);

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

        $this->onChange($config);
    }
}
