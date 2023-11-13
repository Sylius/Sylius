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

namespace spec\Sylius\Bundle\ApiBundle\Serializer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPriceHistoryConfigInterface;
use Sylius\Component\Core\Model\ShopBillingDataInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class ChannelDenormalizerSpec extends ObjectBehavior
{
    private const ALREADY_CALLED = 'sylius_channel_denormalizer_already_called';

    function let(FactoryInterface $configFactory, FactoryInterface $shopBillingDataFactory): void
    {
        $this->beConstructedWith($configFactory, $shopBillingDataFactory);
    }

    function it_does_not_support_denormalization_when_the_denormalizer_has_already_been_called(): void
    {
        $this->supportsDenormalization([], ChannelInterface::class, context: [self::ALREADY_CALLED => true])->shouldReturn(false);
    }

    function it_does_not_support_denormalization_when_data_is_not_an_array(): void
    {
        $this->supportsDenormalization('string', ChannelInterface::class)->shouldReturn(false);
    }

    function it_does_not_support_denormalization_when_type_is_not_a_channel(): void
    {
        $this->supportsDenormalization([], 'string')->shouldReturn(false);
    }

    function it_throws_an_exception_when_denormalizing_an_object_that_is_not_a_channel(
        DenormalizerInterface $denormalizer,
    ): void {
        $this->setDenormalizer($denormalizer);

        $denormalizer->denormalize([], 'string', null, [self::ALREADY_CALLED => true])->willReturn(new \stdClass());

        $this->shouldThrow(\InvalidArgumentException::class)->during('denormalize', [[], 'string']);
    }

    function it_returns_channel_as_is_when_shop_billing_data_and_channel_price_history_config_has_already_been_set(
        DenormalizerInterface $denormalizer,
        FactoryInterface $configFactory,
        ShopBillingDataInterface $shopBillingData,
        ChannelPriceHistoryConfigInterface $config,
        ChannelInterface $channel,
    ): void {
        $this->setDenormalizer($denormalizer);

        $channel->getChannelPriceHistoryConfig()->willReturn($config);
        $channel->getShopBillingData()->willReturn($shopBillingData);

        $channel->setChannelPriceHistoryConfig(Argument::any())->shouldNotBeCalled();
        $configFactory->createNew()->shouldNotBeCalled();

        $denormalizer->denormalize([], ChannelInterface::class, null, [self::ALREADY_CALLED => true])->willReturn($channel);

        $this->denormalize([], ChannelInterface::class)->shouldReturn($channel);
    }

    function it_adds_a_new_channel_price_history_config_when_channel_has_none(
        DenormalizerInterface $denormalizer,
        FactoryInterface $configFactory,
        ShopBillingDataInterface $shopBillingData,
        ChannelPriceHistoryConfigInterface $config,
        ChannelInterface $channel,
    ): void {
        $this->setDenormalizer($denormalizer);

        $channel->getChannelPriceHistoryConfig()->willReturn(null);
        $channel->getShopBillingData()->willReturn($shopBillingData);

        $configFactory->createNew()->willReturn($config);
        $channel->setChannelPriceHistoryConfig($config)->shouldBeCalled();

        $denormalizer->denormalize([], ChannelInterface::class, null, [self::ALREADY_CALLED => true])->willReturn($channel);

        $this->denormalize([], ChannelInterface::class)->shouldReturn($channel);
    }

    function it_adds_a_new_shop_billing_data_when_channel_has_none(
        DenormalizerInterface $denormalizer,
        FactoryInterface $shopBillingDataFactory,
        ShopBillingDataInterface $shopBillingData,
        ChannelPriceHistoryConfigInterface $config,
        ChannelInterface $channel,
    ): void {
        $this->setDenormalizer($denormalizer);

        $channel->getChannelPriceHistoryConfig()->willReturn($config);
        $channel->getShopBillingData()->willReturn(null);

        $shopBillingDataFactory->createNew()->willReturn($shopBillingData);
        $channel->setShopBillingData($shopBillingData)->shouldBeCalled();

        $denormalizer->denormalize([], ChannelInterface::class, null, [self::ALREADY_CALLED => true])->willReturn($channel);

        $this->denormalize([], ChannelInterface::class)->shouldReturn($channel);
    }
}
