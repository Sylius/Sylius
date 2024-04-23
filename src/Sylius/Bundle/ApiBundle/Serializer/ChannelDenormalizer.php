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

namespace Sylius\Bundle\ApiBundle\Serializer;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPriceHistoryConfigInterface;
use Sylius\Component\Core\Model\ShopBillingDataInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Webmozart\Assert\Assert;

final class ChannelDenormalizer implements ContextAwareDenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    private const ALREADY_CALLED = 'sylius_channel_denormalizer_already_called';

    /**
     * @param FactoryInterface<ChannelPriceHistoryConfigInterface> $channelPriceHistoryConfigFactory
     * @param FactoryInterface<ShopBillingDataInterface> $shopBillingDataFactory
     */
    public function __construct(
        private FactoryInterface $channelPriceHistoryConfigFactory,
        private FactoryInterface $shopBillingDataFactory,
    ) {
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return
            !isset($context[self::ALREADY_CALLED]) &&
            is_array($data) &&
            is_a($type, ChannelInterface::class, true)
        ;
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = [])
    {
        $context[self::ALREADY_CALLED] = true;
        $data = (array) $data;

        $channel = $this->denormalizer->denormalize($data, $type, $format, $context);
        Assert::isInstanceOf($channel, ChannelInterface::class);
        if (null === $channel->getChannelPriceHistoryConfig()) {
            /** @var ChannelPriceHistoryConfigInterface $channelPriceHistoryConfig */
            $channelPriceHistoryConfig = $this->channelPriceHistoryConfigFactory->createNew();
            $channel->setChannelPriceHistoryConfig($channelPriceHistoryConfig);
        }

        if (null === $channel->getShopBillingData()) {
            /** @var ShopBillingDataInterface $shopBillingData */
            $shopBillingData = $this->shopBillingDataFactory->createNew();
            $channel->setShopBillingData($shopBillingData);
        }

        return $channel;
    }
}
