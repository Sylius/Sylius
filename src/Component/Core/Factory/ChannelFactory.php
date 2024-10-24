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

namespace Sylius\Component\Core\Factory;

use Sylius\Component\Channel\Factory\ChannelFactoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPriceHistoryConfigInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

/**
 * @template T of ChannelInterface
 *
 * @implements ChannelFactoryInterface<T>
 */
final class ChannelFactory implements ChannelFactoryInterface
{
    /**
     * @param FactoryInterface<T> $decoratedFactory
     * @param FactoryInterface<ChannelPriceHistoryConfigInterface> $channelPriceHistoryConfigFactory
     */
    public function __construct(
        private FactoryInterface $decoratedFactory,
        private string $defaultCalculationStrategy,
        private FactoryInterface $channelPriceHistoryConfigFactory,
    ) {
    }

    /** @inheritdoc */
    public function createNew(): ChannelInterface
    {
        $channel = $this->decoratedFactory->createNew();
        $channel->setTaxCalculationStrategy($this->defaultCalculationStrategy);

        /** @var ChannelPriceHistoryConfigInterface $channelPriceHistoryConfig */
        $channelPriceHistoryConfig = $this->channelPriceHistoryConfigFactory->createNew();
        $channel->setChannelPriceHistoryConfig($channelPriceHistoryConfig);

        return $channel;
    }

    public function createNamed(string $name): ChannelInterface
    {
        $channel = $this->createNew();
        $channel->setName($name);
        Assert::isInstanceOf($channel, ChannelInterface::class);

        return $channel;
    }
}
